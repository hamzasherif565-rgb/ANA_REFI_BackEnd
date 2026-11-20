# ANA REFI API Documentation

This document describes the API currently implemented in this Laravel repository. The route prefix is `/api/v1` (Laravel's `/api` prefix plus the version prefix). All request and response examples use JSON unless an endpoint is explicitly marked as a redirect or multipart upload.

## Base URL and conventions

Set `BASE_URL` to the application origin, for example `http://localhost:8000`. The full endpoint is `${BASE_URL}/api/v1/...`.

For JSON requests, send `Accept: application/json` and `Content-Type: application/json`. Protected endpoints use Laravel Sanctum personal access tokens:

```http
Authorization: Bearer <token>
```

Most resource endpoints return `{ "data": ... }`. Laravel resource collections return `data`, `links`, and `meta`; a few endpoints return custom pagination metadata. A successful delete returns `204 No Content`. Validation errors normally return `422` with `{ "message": "...", "errors": { "field": ["..."] } }`. Other common responses are `401` (missing/invalid authentication), `403` (not allowed), and `404` (resource not found or deliberately hidden).

IDs are UUIDs. Dates accept Laravel date formats; send ISO-8601 timestamps where a time is needed. Prices and amounts are integer EGP values.

## Authentication

| Method | Endpoint | Access | Request / behavior |
| --- | --- | --- | --- |
| POST | `/auth/register` | Public; 10 requests/minute | JSON: `first_name`, `last_name`, `email`, `phone`, `password`, `password_confirmation`, `account_type` (`traveler` or `host`). Host registration requires `national_id` (14 digits). Optional `id_front` and `id_back` are storage path strings; when both are supplied, host verification is created as pending. Returns `201` with `data` (user/profile/roles), `token`, and `token_type: Bearer`. |
| POST | `/auth/login` | Public; 10 requests/minute | JSON: `email`, `password`. Returns the user payload and a bearer token. Invalid credentials return `422`; blocked accounts return `403`. |
| GET | `/auth/google/redirect` | Public | Redirects to Google. Optional `redirect_uri` must have the same origin as configured `GOOGLE_FRONTEND_URL` (see environment configuration). The callback returns to the allowed frontend URL with `#api_token=<token>`. Requires Google OAuth configuration. |
| GET | `/auth/google/callback` | Public OAuth callback | Google redirects here with its authorization `code` and state. The state is single-use and expires after 10 minutes. |
| POST | `/auth/logout` | Authenticated | Revokes the current token; `204`. |
| GET | `/auth/user` | Authenticated | Returns `{ data: { user, profile, roles } }`, including the user's private phone. |

Registration/login payload shape:

```json
{
  "data": {
    "user": { "id": "uuid", "email": "person@example.com" },
    "profile": {
      "id": "uuid", "display_name": "First Last", "avatar_url": null,
      "bio": null, "account_type": "traveler", "role_selected": true,
      "is_blocked": false, "age": null, "location": null,
      "email": "person@example.com", "phone": "+201000000000"
    },
    "roles": ["traveler"]
  },
  "token": "...",
  "token_type": "Bearer"
}
```

## Profiles and uploads

| Method | Endpoint | Access | Request / behavior |
| --- | --- | --- | --- |
| GET | `/profile` | Authenticated | Current user's profile, with `email` and private `phone`. |
| PATCH | `/profile` | Authenticated | Optional `display_name`, `avatar_url` (URL), `bio`, `age` (13–100), `location`, and `phone`. `account_type`, `role_selected`, and `is_blocked` cannot be changed here. Setting phone to null/empty removes the saved contact. |
| PUT | `/profile/contact` | Authenticated | `{ "phone": "..." }`; upserts the private contact. |
| POST | `/profile/become-host` | Authenticated | Adds the host role and sets the profile account type to host. |
| POST | `/profile/select-role` | Authenticated | `{ "account_type": "traveler" }` or `host`. A selected role cannot later be changed through this endpoint. |
| POST | `/uploads/listing-images` | Authenticated | Multipart form field `file` (image, max 10 MB). Returns `201` with `{ data: { path, url } }`. Stored on the public disk. |
| POST | `/uploads/identity-documents` | Authenticated | Multipart fields `file` (image, max 10 MB) and `side` (`front` or `back`). Returns `201` with private storage `path`. |
| PUT | `/host/verification` | Authenticated | `first_name`, `last_name`, `national_id` (14 digits), `id_front_path`, `id_back_path`; saves verification as pending. |

## Public catalog and discovery

| Method | Endpoint | Access | Request / behavior |
| --- | --- | --- | --- |
| GET | `/stats` | Public | Aggregate counts: travelers, hosts, bookings, approved volunteers, active listings, competitions. |
| GET | `/profiles?ids=<uuid>,<uuid>` | Public | Returns public profile fields (id, display name, avatar, bio, account type, created date) for the supplied comma-separated IDs. Without IDs, an authenticated caller gets their own profile; an anonymous caller gets an empty array. |
| GET | `/profiles/{profile}` | Public | Route exists, but the current handler does not use the path parameter; use `/profiles?ids=...` to select profiles. |
| GET | `/listings` | Public; authenticated users also see their own non-active listings | Filters: `type` (`stay`, `experience`, `product`), `category`, `search` (title/title_en/location), `min_price`, `max_price`, `sort` (`newest`, `price_asc`, `price_desc`), `per_page` (1–100; default 20). `status` is accepted by validation but does not currently filter results. |
| GET | `/listings/featured` | Public | Up to three active featured products. |
| GET | `/listings/{listing}` | Public | Active listing details; the owning host/admin can also view a non-active listing. |
| GET | `/hosts/{host}/listings` | Public | Active listings for the host, 20 per page. |
| GET | `/competitions` | Public; authenticated host can also see own drafts | Filters: `search`, `category`, `status` (`upcoming`, `open`, `closed`, `completed`, `cancelled`), `per_page` (1–100; default 30). Drafts are excluded by default. Ordered featured first, then start date. |
| GET | `/competitions/{competition}` | Public | Draft is hidden except from its owner/admin. |
| GET | `/listings/{listing}/reviews` | Public | Newest first; `per_page` defaults to 20 (maximum 100). |
| GET | `/community/posts` | Public | Newest first; `per_page` defaults to 60 (maximum 100). |
| POST | `/contact-messages` | Public; 10 requests/minute | JSON: `name` (max 150), `email`, `message` (5–5000 characters). Returns `201`. |
| GET | `/volunteers` | Public; authenticated admins see all applications | Anonymous users and non-admin users only see approved volunteers. |

Listing collection items contain `id`, `host_id`, `type`, bilingual title/description, category, price, discount, capacity, location, images, dates, `is_featured`, status, timestamps, optional host summary, average rating, and review count.

## Listings and competitions (host/admin)

| Method | Endpoint | Access | Request / behavior |
| --- | --- | --- | --- |
| POST | `/listings` | Authenticated; owner is the caller | Required: `type`, `title`, `price`. Optional: `title_en`, `description`, `description_en`, `category`, `discount` (0–100), `capacity` (at least 1), `location`, `images` (up to 6 strings), `starts_at`, `ends_at` (not before start), `status` (`active`, `draft`, `blocked`). `is_featured` is prohibited. Returns `201`. |
| PATCH | `/listings/{listing}` | Owner/admin | Same fields, all optional. Owner/admin only. |
| DELETE | `/listings/{listing}` | Owner/admin | Deletes listing; `204`. |
| POST | `/competitions` | Authenticated; owner is caller | Required: `title`, `category`, `location`, `starts_at`, `ends_at` (after start). Optional bilingual description/title, `prize_amount`, `entry_fee` (non-negative integers), `capacity` (positive integer), `image_url` (URL), `status` (`draft`, `upcoming`, `open`, `closed`, `completed`, `cancelled`), `coming_soon` (boolean). `featured` is prohibited. Returns `201`. |
| PATCH | `/competitions/{competition}` | Owner/admin | Competition fields above are optional on update. Changing status creates notifications for users with non-cancelled competition bookings. |
| DELETE | `/competitions/{competition}` | Owner/admin | Deletes competition; `204`. |
| GET | `/competitions/{competition}/participants` | Competition owner/admin | Paginated bookings, 50 per page. |

## Bookings

| Method | Endpoint | Access | Request / behavior |
| --- | --- | --- | --- |
| GET | `/bookings` | Authenticated | Current user's bookings, newest first; `per_page` defaults to 20 (max 100). |
| POST | `/bookings` | Authenticated | Required: `item_type` (`competition`, `stay`, `experience`, `product`), `item_id` UUID. Optional: `booking_date` (today or later), `guests` (1–100), `quantity` (1–100), `notes` (max 2000). Server calculates amount and host; client cannot provide either. Creates pending booking and a confirmation notification; returns `201`. |
| GET | `/bookings/{booking}` | Booking owner, host, or admin | Booking and resolved item summary. |
| PATCH | `/bookings/{booking}` | Booking owner/host/admin, subject to allowed status changes | Required `status` (`pending`, `confirmed`, `cancelled`, `completed`); optional `notes`. Traveler may confirm/cancel; host may confirm/cancel/complete; admin may set any listed status. Completed/cancelled bookings cannot be changed. |
| DELETE | `/bookings/{booking}` | Booking host/admin | Deletes booking; `204`. |
| GET | `/host/bookings` | Host | Host's bookings; `per_page` defaults to 100 (max 100). Includes guest name and phone. |
| GET | `/host/stats` | Host | Host totals: revenue, pending revenue, booking count, listing count, average booking value. |

Booking creation rejects inactive/mismatched listings, a host booking their own listing, full competitions, and quantities above listing capacity. Competition bookings require status `open` or `upcoming`; competition capacity is checked against participant count. Product amount uses discounted price times quantity; other listing amount uses discounted price times guests. Competition amount uses entry fee.

## Favorites, reviews, notifications, community

| Method | Endpoint | Access | Request / behavior |
| --- | --- | --- | --- |
| GET | `/favorites` | Authenticated | Current user's favorites with resolved item summaries. |
| PUT | `/favorites` | Authenticated | `{ "item_id": "uuid", "item_type": "stay|experience|product|competition" }`; adds favorite (existing favorite is reused). Returns `201`. |
| DELETE | `/favorites/{itemType}/{itemId}` | Authenticated | Removes matching favorite belonging to caller; `204`. |
| DELETE | `/favorites/by-id/{favorite}` | Authenticated | Removes favorite only if owned by caller; `204`. |
| POST | `/listings/{listing}/reviews` | Authenticated | `rating` (1–5), optional `comment` (max 500), optional `image_url` (URL). Returns `201`. |
| PATCH | `/reviews/{review}` | Review author/admin | Optional `rating`, `comment`, `image_url`. |
| DELETE | `/reviews/{review}` | Review author/admin | Deletes review; `204`. |
| GET | `/notifications` | Authenticated | Current user's notifications; `per_page` defaults to 30 (max 100). Includes `meta.unread_count`. |
| PATCH | `/notifications/read-all` | Authenticated | Marks all caller's unread notifications as read; returns `{ data: { updated } }`. |
| DELETE | `/notifications/{notification}` | Authenticated | Deletes only caller's notification; `204`. |
| POST | `/community/posts` | Authenticated | Required `title` (max 255), `body` (max 10000); optional `image_url` (URL), `listing_id` (UUID). The linked listing must be active or owned by caller. Returns `201`. |
| DELETE | `/community/posts/{post}` | Post author/admin | Deletes post; `204`. |

## Volunteers and feature requests

| Method | Endpoint | Access | Request / behavior |
| --- | --- | --- | --- |
| POST | `/volunteers/applications` | Authenticated | Required: `full_name`, `email`, `phone`, `interest_area`, `availability`, `motivation`; optional: `age`, `avatar_url`, `bio`, `location`, `skills[]`, `languages[]`, `experience`, `accessibility`. Upserts caller's application with status pending; returns `201`. |
| GET | `/volunteers/application` | Authenticated | Returns the caller's volunteer application or `null`. |
| GET | `/volunteer-service-requests` | Authenticated | Service requests assigned to caller as volunteer; 30 per page. |
| POST | `/volunteer-service-requests` | Authenticated | Required: approved `volunteer_id`, `requested_date` (today or later), `location`, `reason`; optional `requested_time`, `people_count` (1–100), `notes`. Requester is derived from token. Returns `201`. |
| PATCH | `/volunteer-service-requests/{serviceRequest}` | Assigned volunteer/admin | Required `status`: `pending`, `approved`, `rejected`, `completed`, or `cancelled`. |
| POST | `/feature-requests` | Host | Required `listing_id` (owned product UUID), `amount` (non-negative integer), `payment_method` (max 40). Payment and request status start pending. Returns `201`. |
| GET | `/host/feature-requests` | Host (own requests); admin (all) | Paginated feature requests. |
| GET | `/admin/feature-requests` | Admin | Paginated feature requests. |
| PATCH | `/admin/feature-requests/{featureRequest}` | Admin | Required `status` (`approved`, `rejected`, `pending`); optional `admin_note` (max 2000). Approval marks the listing featured. |

## Admin endpoints

All `/admin/...` routes require an authenticated admin role. The same applies to the `admin` route group below.

| Method | Endpoint | Request / behavior |
| --- | --- | --- |
| GET | `/admin/listings` | All listings, paginated (`per_page` default 100, max 100). |
| GET | `/admin/users` | Profiles with user email, phone, and roles; optional `search` on display name; `per_page` default 30 (max 100). |
| PATCH | `/admin/users/{user}/block` | `{ "is_blocked": true }` or false; cannot block own account. |
| PATCH | `/admin/listings/{listing}/featured` | `{ "is_featured": true }` or false. Only active products can be featured; maximum three active featured products. |
| GET | `/admin/volunteers` | All volunteer applications, paginated. |
| PATCH | `/admin/volunteers/{volunteer}` | `{ "status": "pending|approved|rejected" }`. |
| DELETE | `/admin/volunteers/{volunteer}` | Deletes application; `204`. |
| GET | `/admin/feature-requests` | See feature request section. |

Host-only endpoints also include `GET /host/listings` and `GET /host/competitions` (each returns the caller's paginated resources).

## Environment configuration

Set `APP_URL` to the API origin. Google OAuth uses the Laravel service configuration sourced from `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, and `GOOGLE_FRONTEND_URL`. Ensure Sanctum token authentication and public storage URLs are configured for the deployment. Never send payment card details to this API; this code has no card payment endpoint.

## Implementation notes

- The API requirements in `API_REQUIREMENTS.md` describe broader frontend expectations. This document follows the current routes and controller behavior. Notable differences: there is no `/admin/overview` endpoint; `/admin/users` is the implemented user listing; competitions do not accept a sort parameter; featured flags are admin-controlled; and favorites return an array rather than pagination.
- These notes describe observable implementation behavior and do not imply that behavior has been exercised against a production database.

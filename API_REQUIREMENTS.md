# ANA REFI API Requirements

This document records the API contract inferred from the React repository `hamzasherif565-rgb/ana-refi-6273a424` (cloned and inspected before implementation). The frontend is a TanStack Start / React 19 application. It currently uses Supabase Auth, PostgREST CRUD, Supabase Storage, React Query, and server functions. The application is bilingual (Arabic/English) and supports stays, experiences, products, competitions, bookings, host/admin dashboards, community, reviews, notifications, favorites, volunteer features, contact submissions, and AI-assisted discovery.

The existing Laravel schema is retained. Imported tables have physical `public_` / `auth_` prefixes. API serializers should expose the existing frontend field names without those prefixes. No new database schema is required by this contract.

## Contract conventions

- Base path: `/api/v1`.
- JSON responses use `{ "data": ... }`; paginated collections also include Laravel pagination metadata/links.
- Authentication: `Authorization: Bearer <Sanctum token>` for private operations. Public reads/contact submissions do not require a token.
- Validation errors: HTTP 422 with Laravel's `{ "message": ..., "errors": { field: [...] } }` structure.
- Unauthenticated: 401; unauthorized: 403; missing resource: 404; successful create: 201; successful delete: 204.
- Listing types: `stay`, `experience`, `product`; competition records use their own table. Booking item types additionally allow `competition`.
- IDs are UUIDs. Times are ISO-8601; money amounts are integer EGP values as represented by the current schema.

## 1. Authentication, profiles, and roles

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| POST | `/auth/register` | Traveler and host sign-up; public | `first_name`, `last_name`, `email`, `phone`, `password`, `password_confirmation`, `account_type` (`traveler` or `host`). Host also sends `national_id`, `id_front`, `id_back`. Returns user/profile/roles and bearer token; host verification status is pending. |
| POST | `/auth/login` | Email/password sign-in; public | `email`, `password`. Returns user/profile/roles and bearer token. |
| GET | `/auth/google/redirect` | Google sign-in buttons; public | `redirect_uri` must match configured frontend origin. Uses Google OAuth and returns to that frontend route with a one-time bearer token in the URL fragment. Requires `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, and `FRONTEND_URL`. |
| POST | `/auth/logout` | Sign out; authenticated | Revokes current token; 204. |
| GET | `/auth/user` | Restore session/current user; authenticated | Returns user, profile, role list, and contact phone only to that user. |
| GET | `/profile` | Profile hook and settings; authenticated | Current profile fields: `display_name`, `avatar_url`, `bio`, `account_type`, `role_selected`, `is_blocked`, optional `age`, `location`. |
| PATCH | `/profile` | Settings/role onboarding; authenticated | Profile edits; role selection is allowed once for a non-admin, account type cannot be changed by the user after selection. |
| PUT | `/profile/contact` | Private phone persistence; authenticated | `{ "phone": "..." }`; returns saved contact. |
| POST | `/profile/become-host` | Host upgrade action; authenticated | Adds host role and updates profile account type when permitted. |
| GET | `/users` | Admin user table; admin | Paginated profiles, email, phone, role/account type, blocked status, created date. Search/filter supported. |
| PATCH | `/users/{user}/block` | Admin moderation; admin | `{ "is_blocked": true|false }`; blocks/unblocks account. |

Google OAuth appears through a Lovable managed OAuth broker. There is no provider-independent OAuth callback contract in the React source. Preserve the UI integration only if provider credentials/broker behavior are configured; never expose provider secrets in the browser. The host National ID uploads are private and never returned in normal profile responses.

## 2. Listings and discovery

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/listings` | Home featured content, stays/experiences/products galleries, maps, AI context; public | Filters: `type`, `status`, `category`, `search` (Arabic/English title and location), `min_price`, `max_price`, `sort` (`newest`, `price_asc`, `price_desc`), `page`, `per_page`. Public results default to active; return listing columns, images, host summary, and pagination. |
| GET | `/listings/featured` | Home featured products; public | Active featured products (admin caps featured items at three). |
| GET | `/listings/{listing}` | Details, booking summary, review context; public | Listing with host summary and paginated reviews. |
| GET | `/hosts/{host}/listings` | Host public profile/host-area data; public | Active listings for the host, paginated. |
| POST | `/listings` | Host create; host/admin | `type`, bilingual `title`, `description`, `category`, `price`, `discount`, `capacity`, `location`, `images[]` (up to 6 uploaded URLs), `starts_at`, `ends_at`, `status`. Host ID is always derived from auth. |
| PATCH | `/listings/{listing}` | Host edit/publish; owner/admin | Same editable fields; owner/admin only. |
| DELETE | `/listings/{listing}` | Host delete; owner/admin | 204. |
| POST | `/uploads/listing-images` | ImageUploader; host/admin | Multipart `file`; returns storage path and URL suitable for `listings.images`. |

Gallery behavior required by the frontend: type/status filtering, Arabic and English search, category and price filters, newest/ascending/descending sort, pagination, current listing details fields, and favorite state integration. Rating is currently displayed as a static frontend fallback (4.8) in `mapListingRow`; do not fabricate a database rating. If a true aggregate is available, expose it from reviews.

## 3. Competitions

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/competitions` | Competition page / AI context; public | Search, category, status, `page`, `per_page`, sort featured then start date. Public endpoint excludes drafts by default. |
| GET | `/competitions/{competition}` | Competition details; public | Competition columns, host summary, participants count/capacity. |
| POST | `/competitions` | Host dashboard create; host/admin | `title`, `title_en`, `description`, `description_en`, `category`, `location`, `starts_at`, `ends_at`, `prize_amount`, `entry_fee`, `capacity`, `image_url`, `status`, `featured`, `coming_soon`. Host ID is derived from auth. |
| PATCH | `/competitions/{competition}` | Host dashboard edit; owner/admin | Editable fields above. |
| DELETE | `/competitions/{competition}` | Host dashboard delete; owner/admin | 204. |
| GET | `/competitions/{competition}/participants` | Host participant management; owner/admin | Paginated participant bookings with user/profile details appropriate to host. |

Competition card fields include bilingual title, image, category, dates, location, prize, entry fee, participant count/capacity, status, featured, and coming-soon. Drafts remain hidden from public reads. Joining is implemented by creating a competition booking (see booking API), not direct client writes to the competition row.

## 4. Bookings and checkout

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/bookings` | Traveler dashboard; authenticated | Own bookings, newest first, with resolved listing or competition title/image and host summary. Supports pagination. |
| POST | `/bookings` | Listing booking/product order/competition join; authenticated | `item_type`, `item_id`, optional `booking_date`, `guests`, `quantity`, `notes`. Server derives user, host, listing reference, and amount from current item pricing; never trusts client amount/owner. Returns created pending booking. |
| GET | `/bookings/{booking}` | Payment page; owner, host of booking, or admin | Booking with item summary. |
| PATCH | `/bookings/{booking}` | Payment confirmation/cancel, host status change; scoped | Traveler may confirm simulated card checkout or cancel eligible own pending booking; host may set `confirmed`, `cancelled`, or `completed` for own booking. Cash leaves pending. No card data is stored. |
| DELETE | `/bookings/{booking}` | Host dashboard currently exposes delete; owning host/admin | 204, subject to safe deletion policy. |
| GET | `/host/bookings` | Host dashboard; host/admin | Paginated bookings for host, guest display name and phone (restricted to booking host/admin), item summary, date, guests, amount, status. |
| GET | `/host/stats` | Host dashboard; host/admin | Revenue, pending revenue, booking count, listing count, average booking value; scoped to authenticated host. |

Booking creation must validate item existence/status, competition capacity/duplicate active entry and available schema relationships. Booking/status changes trigger user and host notifications according to the existing notification behavior. Card number, expiry, and CVV are client-only validation data and must never be transmitted or persisted.

## 5. Favorites

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/favorites` | Favorites hook/page/dashboard; authenticated | Current user's favorite rows and resolved listing/competition summaries, grouped or filterable by `item_type`. |
| PUT | `/favorites` | Card/detail toggle; authenticated | `{ "item_id": "uuid", "item_type": "stay|experience|product|competition" }`; idempotently adds favorite. |
| DELETE | `/favorites/{itemType}/{itemId}` | Card/detail toggle; authenticated | Removes only current user's matching favorite; 204. |

Favorite visibility and mutation are owner-scoped. The source supports listing and competition favorite resolution.

## 6. Reviews

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/listings/{listing}/reviews` | Listing detail/home reviews; public | Newest first, author profile summary and created date; paginated. |
| POST | `/listings/{listing}/reviews` | Listing review form; authenticated | `rating` 1–5, `comment` max 500, optional `image_url`; user derived from auth. |
| PATCH | `/reviews/{review}` | Review author/admin; authenticated | `rating`, `comment`, `image_url` fields. |
| DELETE | `/reviews/{review}` | Review author/admin; authenticated | 204. |

Review photos use the public listing image upload flow. The source's visible interactions include read and create; edit/delete remain part of documented feature requirements only if exposed by the current UI.

## 7. Notifications

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/notifications` | Notification center/navbar/dashboard; authenticated | Current user's notifications newest first, `unread_count`, paginated; dashboard can request `per_page=20`. |
| PATCH | `/notifications/read-all` | Notification center; authenticated | Marks only current user's unread records read; returns count. |
| DELETE | `/notifications/{notification}` | Notification center; authenticated | Deletes only current user's notification; 204. |

Notifications cover booking creation/status changes and competition updates where source triggers exist. The source does not implement message persistence/chat between users.

## 8. Community

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/community/posts` | Community feed; public | Newest first, up to 60 by default, author profile summary, optional listing summary; paginated. |
| POST | `/community/posts` | Share action; authenticated | `title`, `body`, optional `image_url`, `listing_id`; user derived from auth. |
| DELETE | `/community/posts/{post}` | Owner/admin; authenticated | 204. |

## 9. Contact and feature requests

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| POST | `/contact-messages` | Home contact forms; public | `name`, valid `email`, `message` with schema-aligned length validation; returns 201. |
| POST | `/feature-requests` | Host asks admin to feature a product; authenticated host | `listing_id`, `amount`, `payment_method`; host derived from auth; initial payment/status controlled server-side. |
| GET | `/host/feature-requests` | Host dashboard; host/admin | Own requests, newest first. |
| GET | `/admin/feature-requests` | Admin review; admin | Paginated all requests. |
| PATCH | `/admin/feature-requests/{request}` | Admin review; admin | `status`, optional `admin_note`. |

## 10. Volunteers

The frontend has public volunteer discovery and application, authenticated service requests, and admin moderation.

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/volunteers` | Volunteer section; public | Approved volunteer profiles, optional search/interest/location filters. |
| POST | `/volunteers/applications` | Apply/update volunteer profile; authenticated | `full_name`, `email`, `phone`, `interest_area`, `availability`, `motivation`, and optional profile fields (`age`, `avatar_url`, `bio`, `location`, `skills`, `languages`, `experience`, `accessibility`). |
| GET | `/admin/volunteers` | Admin volunteers panel; admin | All applications, newest first. |
| PATCH | `/admin/volunteers/{volunteer}` | Admin approval/moderation; admin | `status`. |
| DELETE | `/admin/volunteers/{volunteer}` | Admin; admin | 204. |
| GET | `/volunteer-service-requests` | Volunteer requests panel; volunteer/admin | Assigned requests for the volunteer, newest first. |
| POST | `/volunteer-service-requests` | Request volunteer dialog; authenticated | `volunteer_id`, `requested_date`, `requested_time`, `location`, `reason`, `people_count`, optional `notes`; requester derived from auth. |
| PATCH | `/volunteer-service-requests/{request}` | Assigned volunteer/admin; authenticated | `status`, limited to valid workflow states. |

## 11. Admin and aggregate data

| Method | Endpoint | Frontend use / access | Inputs and response |
| --- | --- | --- | --- |
| GET | `/admin/overview` | Admin dashboard; admin | Profiles/users, listings, feature requests, relevant summary counts; paginated collections. |
| PATCH | `/admin/listings/{listing}/featured` | Feature/unfeature dashboard action; admin | `{ "is_featured": true|false }`; enforce maximum of three currently featured active products. |
| GET | `/stats` | Home/admin platform count widgets; public aggregate only | Counts: travelers, hosts, bookings, approved volunteers, active listings, competitions. No personal data. |

The source `FEATURES.md` says the admin page opens without login, but the actual `src/routes/admin.tsx` checks the current user's `user_roles` for `admin` before loading users/listings. Admin writes must therefore be protected; frontend route copy should be aligned with this authorization behavior.

## 12. AI assistant context

`src/lib/ai-chat.functions.ts` calls the Lovable AI gateway server-side and fetches active listings, open/upcoming competitions, and approved volunteers as context. The gateway is not a database API and requires server-side credentials. The Laravel API should expose the same public catalog data through `/listings`, `/competitions`, and `/volunteers`; an AI proxy endpoint is optional unless the gateway is explicitly migrated/configured in Laravel. Do not put AI credentials in the React client.

## 13. Local-only and out-of-scope data

- Language, RTL preference, and light/dark theme are stored in browser local storage; they do not need API endpoints.
- Payment card data is never stored or sent; checkout only updates booking state.
- Maps use listing location text and embedded map URLs; no geocoding API is currently called.
- No direct chat/message table or message UI persistence exists in inspected source.
- Supabase Storage is used for listing images and private identity documents. Listing uploads map to Laravel public media storage; identity uploads must use private storage and owner/admin authorization.

## 14. Existing Laravel model mapping

Frontend logical entities map to existing Eloquent models: `User` → `auth_users`; `Profile` → `public_profiles`; `UserContact` → `public_user_contacts`; `UserRole` → `public_user_roles`; `Listing` → `public_listings`; `Competition` → `public_competitions`; `Booking` → `public_bookings`; `Favorite` → `public_favorites`; `Review` → `public_reviews`; `Notification` → `public_notifications`; `CommunityPost` → `public_community_posts`; `ContactMessage` → `public_contact_messages`; `HostVerification` → `public_host_verifications`; `FeatureRequest` → `public_feature_requests`; volunteer entities → `public_volunteer_requests` and `public_volunteer_service_requests`.

The database conversion notes that its source snapshot lacks original row-level-security expressions and some trigger function bodies. API authorization and notification behavior must therefore be enforced explicitly in Laravel and validated against the schema constraints; assumptions should not be treated as verified original Supabase behavior.

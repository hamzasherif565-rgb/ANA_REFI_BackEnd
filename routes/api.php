<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\EngagementController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::get('google/redirect', [AuthController::class, 'googleRedirect'])->name('api.v1.auth.google.redirect');
        Route::get('google/callback', [AuthController::class, 'googleCallback'])->name('api.v1.auth.google.callback');
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'user']);
        });
    });

    Route::get('stats', [AdminController::class, 'stats']);
    Route::get('profiles', [AdminController::class, 'publicProfiles']);
    Route::get('profiles/{profile}', [AdminController::class, 'publicProfiles']);
    Route::get('listings', [CatalogController::class, 'listings']);
    Route::get('listings/featured', [CatalogController::class, 'featured']);
    Route::get('listings/{listing}', [CatalogController::class, 'listing']);
    Route::get('hosts/{host}/listings', [CatalogController::class, 'hostListings']);
    Route::get('listings/{listing}/reviews', [EngagementController::class, 'reviews']);
    Route::get('competitions', [CatalogController::class, 'competitions']);
    Route::get('competitions/{competition}', [CatalogController::class, 'competition']);
    Route::post('contact-messages', [EngagementController::class, 'contact'])->middleware('throttle:10,1');
    Route::get('volunteers', [AdminController::class, 'volunteers']);
    Route::get('community/posts', [EngagementController::class, 'posts']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('profile', [ProfileController::class, 'show']);
        Route::patch('profile', [ProfileController::class, 'update']);
        Route::put('profile/contact', [ProfileController::class, 'contact']);
        Route::post('profile/become-host', [ProfileController::class, 'becomeHost']);
        Route::post('profile/select-role', [ProfileController::class, 'selectRole']);

        Route::post('uploads/listing-images', [CatalogController::class, 'uploadListingImage']);
        Route::post('uploads/identity-documents', [CatalogController::class, 'uploadIdentity']);
        Route::put('host/verification', [AdminController::class, 'saveHostVerification']);
        Route::post('listings', [CatalogController::class, 'storeListing']);
        Route::patch('listings/{listing}', [CatalogController::class, 'updateListing']);
        Route::delete('listings/{listing}', [CatalogController::class, 'deleteListing']);
        Route::post('competitions', [CatalogController::class, 'storeCompetition']);
        Route::patch('competitions/{competition}', [CatalogController::class, 'updateCompetition']);
        Route::delete('competitions/{competition}', [CatalogController::class, 'deleteCompetition']);
        Route::get('competitions/{competition}/participants', [BookingController::class, 'participants']);

        Route::get('bookings', [BookingController::class, 'index']);
        Route::post('bookings', [BookingController::class, 'store']);
        Route::get('bookings/{booking}', [BookingController::class, 'show']);
        Route::patch('bookings/{booking}', [BookingController::class, 'update']);
        Route::delete('bookings/{booking}', [BookingController::class, 'destroy']);
        Route::get('host/listings', [AdminController::class, 'hostListings'])->middleware('role:host');
        Route::get('host/competitions', [AdminController::class, 'hostCompetitions'])->middleware('role:host');
        Route::get('host/bookings', [BookingController::class, 'hostIndex'])->middleware('role:host');
        Route::get('host/stats', [BookingController::class, 'hostStats'])->middleware('role:host');

        Route::get('favorites', [EngagementController::class, 'favorites']);
        Route::put('favorites', [EngagementController::class, 'addFavorite']);
        Route::delete('favorites/by-id/{favorite}', [EngagementController::class, 'removeFavoriteById']);
        Route::delete('favorites/{itemType}/{itemId}', [EngagementController::class, 'removeFavorite']);
        Route::post('listings/{listing}/reviews', [EngagementController::class, 'addReview']);
        Route::patch('reviews/{review}', [EngagementController::class, 'updateReview']);
        Route::delete('reviews/{review}', [EngagementController::class, 'deleteReview']);

        Route::get('notifications', [EngagementController::class, 'notifications']);
        Route::patch('notifications/read-all', [EngagementController::class, 'readAll']);
        Route::delete('notifications/{notification}', [EngagementController::class, 'deleteNotification']);
        Route::post('community/posts', [EngagementController::class, 'addPost']);
        Route::delete('community/posts/{post}', [EngagementController::class, 'deletePost']);

        Route::post('feature-requests', [AdminController::class, 'createFeatureRequest'])->middleware('role:host');
        Route::get('host/feature-requests', [AdminController::class, 'featureRequests'])->middleware('role:host');
        Route::post('volunteers/applications', [AdminController::class, 'applyVolunteer']);
        Route::get('volunteer-service-requests', [AdminController::class, 'serviceRequests']);
        Route::post('volunteer-service-requests', [AdminController::class, 'createServiceRequest']);
        Route::patch('volunteer-service-requests/{serviceRequest}', [AdminController::class, 'updateServiceRequest']);

        Route::middleware('role:admin')->prefix('admin')->group(function (): void {
            Route::get('listings', [AdminController::class, 'listings']);
            Route::get('users', [AdminController::class, 'users']);
            Route::patch('users/{user}/block', [AdminController::class, 'block']);
            Route::get('feature-requests', [AdminController::class, 'featureRequests']);
            Route::patch('feature-requests/{featureRequest}', [AdminController::class, 'updateFeatureRequest']);
            Route::patch('listings/{listing}/featured', [AdminController::class, 'toggleFeatured']);
            Route::get('volunteers', [AdminController::class, 'volunteers']);
            Route::patch('volunteers/{volunteer}', [AdminController::class, 'updateVolunteer']);
            Route::delete('volunteers/{volunteer}', [AdminController::class, 'deleteVolunteer']);
        });
    });
});

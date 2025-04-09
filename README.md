# Socialite

A modern social media application built with Laravel 11 and TailwindCSS. Socialite allows users to create profiles, share posts, follow others, like and comment on posts, and chat with connections.

## Features

- User authentication and profiles
- Create and share posts with images
- Follow/unfollow other users
- Like posts and comments
- Comment on posts with nested discussions
- Real-time chat with followers
- Search for users and content
- Responsive design with TailwindCSS

## Architecture

Socialite follows a clean layered architecture with clear separation of concerns:

1. **Controllers** - Handle HTTP requests and route them to appropriate services
2. **Services** - Contain business logic and orchestrate operations
3. **Repositories** - Handle data access and persistence
4. **DTOs** - Transfer data between layers in a consistent format
5. **Models** - Represent database entities and relationships

For more details, see [ARCHITECTURE.md](ARCHITECTURE.md).

## Technology Stack

- **Backend**: Laravel 11
- **Frontend**: TailwindCSS, Alpine.js
- **Database**: MySQL/SQLite
- **Real-time**: Pusher (for chat and notifications)
- **Media**: Intervention Image for image processing

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL or SQLite

### Setup

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/socialite.git
   cd socialite
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Create and setup environment file:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure your database in the `.env` file

6. Run migrations:
   ```
   php artisan migrate
   ```

7. Build assets:
   ```
   npm run build
   ```

## Running the Application

### Development

Run the development server and Vite simultaneously:
```
npm run wow
```

Or use the full development setup with queue listener and logs:
```
composer run dev
```

### Production

For production deployment:
```
npm run build
php artisan optimize
```

## Testing

Run the test suite with:
```
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

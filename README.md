# Mission Management Application

A Laravel-based mission management application with a modern UI built using Bootstrap and Vite.

## Requirements

- PHP >= 8.1
- Composer
- Node.js (Latest LTS version recommended)
- MySQL >= 8.0
- Git

## Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd mission-management
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database**
   Edit the `.env` file and set your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   ```

6. **Run database migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## Development

To start the development server with hot-reload:
```bash
npm run dev
```

## Project Structure

- `app/` - Contains the core code of the application
- `config/` - All configuration files
- `database/` - Database migrations and seeders
- `public/` - Publicly accessible files
- `resources/` - Views, raw assets, and language files
- `routes/` - All route definitions
- `tests/` - Automated tests
- `vendor/` - Composer dependencies
- `node_modules/` - Node.js dependencies

## Dependencies

### PHP Dependencies
- Laravel Framework ^10.10
- Laravel Fortify
- Laravel Jetstream ^4.3
- Laravel Sanctum ^3.3
- Laravel Tinker ^2.8
- Laravel UI

### Node.js Dependencies
- Bootstrap ^5.2.3
- Axios ^1.6.4
- Vite ^5.0.0
- SASS ^1.56.1

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
"# Application-Gestion-des-Mission-Academiques" 

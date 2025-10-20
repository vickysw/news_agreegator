# News Aggregator

A small Laravel-based news aggregator that fetches articles from multiple sources, stores them in the database, and exposes an API to query and consume the aggregated content.

## Key features

- Fetch articles from multiple external news sources (NewsAPI, The Guardian, NYTimes) via dedicated source classes.
- Normalize and store articles, authors and categories in relational tables.
- Avoid duplicate articles by URL; update existing articles when newer data arrives.
- Observer hooks to react to newly created or updated articles (for notifications, indexing, etc.).
- API endpoint to query articles with search, filters (date, category, source, author) and user-preferences.

## Repo layout (important files)

- `app/Services/News/NewsAggregator.php` — orchestrates fetching, deduplication, saving and observer notifications.
- `app/Services/News/Sources/` — implementations to fetch and map remote APIs into `ArticleData` objects.
- `app/Models/Article.php`, `Author.php`, `Category.php` — Eloquent models and relationships.
- `app/Data/ArticleData.php` — DTO for article payloads from remote sources.
- `routes/api.php` — API routes (e.g. `GET /api/articles`).
- `app/Http/Controllers/Api/ArticleController.php` — API controller that implements search & filtering.
- `app/Providers/NewsServiceProvider.php` — registers the aggregator and sources.

## Requirements

- PHP 8.1+ (matches the project's composer.json)
- Composer
- A local database supported by Laravel (MySQL, MariaDB, SQLite, etc.)
- Optional: `php artisan serve` for local serving

## Quick setup (local development)

1. Clone the repository

	```powershell
	git clone <repo-url> news_aggregator
	cd news_aggregator
	```

2. Install composer dependencies

	```powershell
	composer install
	```

3. Copy env and configure

	```powershell
	copy .env.example .env
	# Edit .env to set DB credentials and any API keys (NEWSAPI_API_KEY, GUARDIAN_API_KEY, NYTIMES_API_KEY)
	```

4. Generate app key

	```powershell
	php artisan key:generate
	```

5. Run migrations

	```powershell
	php artisan migrate
	```

6. Start a local server

	```powershell
	php artisan serve
	```

7. The API endpoint for fetching articles is:

	- GET /api/articles

	Example:

	```text
	{base_url}api/articles?search=laravel&category=Tech&from_date=2025-10-01&sources[]=NewsAPI&authors[]=John+Doe
	```

## Routes and API

- `GET /api/articles` — query articles with parameters:
  - `search` — keyword search in title/description/content
  - `from_date`, `to_date` — published_at filters
  - `category`, `categories[]` — filter by category name (or a list)
  - `source`, `sources[]` — filter by source string (or a list)
  - `author`, `authors[]` — filter by author name (or a list)
  - `per_page` — pagination size

Responses use Laravel's pagination JSON object and include `authors` and `categories` relationships.

## Scheduling

- The repository includes a console command `news:fetch` wired in `app/Console/Commands/FetchNewsCommand.php` that calls the aggregator. To run it manually:

  ```powershell
  php artisan news:fetch
  ```

- It's configured in `routes/console.php` / scheduler to run on a schedule (adjust in `app/Console/Kernel.php` if needed).

## Developer notes & tips

- If you change routes, clear and regenerate route cache:

  ```powershell
  php artisan route:clear
  php artisan route:cache
  ```

- If an API route returns 404 while the file and controller look correct, run `php artisan route:list` to inspect registered routes and check for middleware or caching issues.

- The `NewsAggregator` processes articles in chunks to avoid memory issues and synchronizes many-to-many relationships (authors/categories) by name.

# Laravel DB Introspector

A Laravel package to inspect an existing database schema and generate corresponding migration files automatically.

---

## 🚀 Installation

1. Require via Composer:

   ```bash
   composer require your-vendor/laravel-db-introspector
   ```

2. Publish the stub template (optional):

   ```bash
   php artisan vendor:publish \
       --provider="Vendor\DbIntrospector\DatabaseInspectorServiceProvider" \
       --tag=stubs
   ```

## 🎯 Usage

Run the artisan command to generate migrations:

```bash
php artisan db:generate-migrations \
    [--tables=users,posts] \
    [--rewrite] \
    [--output-dir=from_schema]
```

- `--tables=` **(optional)**: Comma-separated list of tables. If omitted, all tables are processed.
- `--rewrite` **(optional)**: Overwrite existing migration files instead of skipping.
- `--output-dir=` **(optional)**: Specify a subdirectory under `database/migrations` to place generated files.

### Examples

- Generate migrations for all tables, skip existing:
  ```bash
  php artisan db:generate-migrations
  ```

- Generate only `users` and `posts`, overwrite any existing:
  ```bash
  php artisan db:generate-migrations --tables=users,posts --rewrite
  ```

- Output migrations to `database/migrations/from_schema`:
  ```bash
  php artisan db:generate-migrations --output-dir=from_schema
  ```

## ⚙️ Configuration

You can customize the stub template located at `database/stubs/db-introspector/migration.stub` after publishing.

The stub uses placeholders:

- `{{class}}` — migration class name
- `{{table}}` — table name
- `{{fields}}` — column definitions

## 🛠️ How It Works

1. **Introspection**: Uses Doctrine DBAL to list tables and columns.
2. **Build**: Maps Doctrine column types to Laravel schema methods.
3. **Generate**: Writes migration files with timestamps to avoid naming conflicts.

## 📄 License

MIT © Your Company

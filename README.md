# OpSpace - Cloud Resources Self-Service Provision/Deprovision

## Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js & NPM
- Terraform CLI
- AWS CLI
- Chosen database

## Local Setup Instructions

1. Clone the repository and install dependencies:
```bash
composer install
npm install
```

2. Copy the environment file and configure it:
```bash
cp .env.example .env
```

3. Configure your `.env` file with:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opspace
DB_USERNAME=root
DB_PASSWORD=

# AWS Configuration
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run database migrations:
```bash
php artisan migrate
```

6. Build assets:
```bash
npm run build
```
7. Serve assets:
```bash
npm run dev
```

8. Start the development server:
```bash
php artisan serve
```

## Usage

1. Visit the admin panel at `http://localhost:8000/admin`
2. Navigate to Resources section
3. Click "Create Resource" to provision a new S3 bucket
4. Fill in the bucket details:
   - Name (must be globally unique)
   - Region
   - Optional: Enable versioning
   - Optional: Add tags

## AWS Configuration

Ensure your AWS credentials have the following permissions:
- s3:CreateBucket
- s3:DeleteBucket
- s3:PutBucketVersioning
- s3:PutBucketPublicAccessBlock
- s3:PutBucketEncryption


# Gym Web Application — AWS Cloud Migration

A PHP-based gym management web application migrated from a local XAMPP 
environment to AWS cloud infrastructure.

## Architecture

![Architecture Diagram](screenshots/architecture.png)

## What Was Migrated

This project was originally running locally using XAMPP with:
- Apache as the web server
- MySQL as the database
- Everything hosted on a single local machine

It was migrated to AWS using a lift-and-shift approach, replacing each 
local component with a managed AWS service.

| Local (XAMPP) | AWS Cloud |
|---|---|
| Apache on localhost | Apache on EC2 (Amazon Linux 2023) |
| MySQL on localhost | Amazon RDS (MySQL) |
| Local machine | EC2 t2.micro instance |

## AWS Services Used

- **EC2** — hosts the Apache web server and PHP application
- **RDS (MySQL)** — managed cloud database replacing local MySQL
- **Security Groups** — controls inbound traffic on ports 80 (HTTP) and 22 (SSH)
- **IAM** — access management for AWS resources
- **AWS Budgets** — billing alarm to monitor and control costs

## How It Works

1. User visits the EC2 public IP in their browser
2. Apache on EC2 serves the PHP application
3. PHP connects to RDS MySQL via PDO using the RDS endpoint
4. RDS returns data and the app renders it to the user

## Setup & Deployment

### Prerequisites
- AWS account with free tier
- EC2 key pair (.pem file)
- PHP 8+ and MySQL 5.7+

### Steps
1. Launch EC2 t2.micro instance (Amazon Linux 2023)
2. Install Apache and PHP:
sudo dnf install httpd php php-mysqlnd -y
sudo systemctl start httpd
sudo systemctl enable httpd
3. Clone this repo to `/var/www/html/` on EC2:
cd /var/www/html
sudo git clone https://github.com/vurbinav/gym-app-aws.git
4. Create RDS MySQL instance (db.t3.micro — free tier)
5. Import database:
mysql -h YOUR-RDS-ENDPOINT -u admin -p gym_db < database/gym_db.sql
6. Create `config.php` on the server (never commit this file):
```php
   <?php
   $host = 'YOUR-RDS-ENDPOINT';
   $dbname = 'gym_db';
   $username = 'admin';
   $password = 'YOUR-PASSWORD';
   $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
```

## Security

- `config.php` is excluded from version control via `.gitignore`
- EC2 SSH access restricted to specific IP only
- RDS instance not publicly accessible from outside VPC
- Security groups follow least-privilege

## Screenshots

![Gym App Running](screenshots/app.png)
![EC2 Instance](screenshots/ec2.png)
![RDS Instance](screenshots/rds.png)
![Security Groups](screenshots/security-groups.png)
![Billing Alarm](screenshots/billing.png)
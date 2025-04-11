# About Rental Notice

Rental notice is the general app for all things rental notices to tenants. The app changes form based on what domain we come into that app with. At launch we are just focusing on past due rental notices. We are only focusing on one state at a time. So the domain name will be specific to the state and the type of rental notice we're offering. We want each user experience to be very specific to a very niche need. However, we're using this generic codebase to support all these different niche verticals. Hence the name Rental Notice.

## Domains

-   oregonpastduerent.com : Main url for past due rental notices in the state of oregon.
-   oregonpastduerent.net : Uses for cold email outreach. Should just redirect to oregonpastduerent.com.

## Setting Up The Database For Local Development

The commands below will create the sqlite database, and seed it with test data.

-   `php artisan migrate`
-   `php artisan db:seed`

## Notice Type Pricing & Plan Dates

Rental Notice uses a plan date system to manage pricing for notice types. This system allows for "grandfathered" pricing where early customers maintain their original pricing plans while newer customers are assigned to more recent pricing plans.

### How It Works

1. **Notice Type Plan Dates**: Each notice type has a `plan_date` field that indicates when that pricing plan was established.

2. **Account Plan Dates**: Each account has a `notice_type_plan_date` field that determines which pricing plan they're assigned to.

3. **New Accounts**: When a new account is created, it's automatically assigned the most recent notice type plan date.

4. **Pricing Determination**: When retrieving available notice types for an account, the system only returns notice types with a `plan_date` less than or equal to the account's `notice_type_plan_date`.

### Benefits

-   **Grandfather Early Customers**: Early customers maintain access to their original pricing plans.
-   **Flexible Pricing Updates**: New pricing plans can be introduced without affecting existing customers.
-   **Plan Management**: Accounts can be upgraded to newer pricing plans if needed.

### Technical Implementation

The system uses the `PricingService` class to handle notice type pricing based on plan dates:

```php
// Get notice types available for an account based on their plan date
$noticeTypes = $pricingService->getNoticeTypesForAccount($account);

// Get the most recent plan date
$mostRecentPlanDate = $pricingService->getMostRecentPlanDate();

// Update an account to the most recent plan
$pricingService->setAccountToMostRecentPlan($account);
```

This ensures consistent pricing and simplifies the management of different pricing tiers over time.

## Building Forms in PDFs

-   `pdfcpu form export templates/ps3817-form.pdf templates/ps3817-form.json`
-   Update the data in `templates/ps3817-form.json`
-   `pdfcpu form fill templates/ps3817-form.pdf templates/ps3817-form.json out.pdf`

# Sysadmin Notes

-   Fly.io restricts where the PHP app can access via `open_basedir`. So in our `Dockerfile` we added this line `sed -i 's|php_admin_value\[open_basedir\] = /var/www/html:/dev/stdout:/tmp|php_admin_value[open_basedir] = /var/www/html:/dev/stdout:/tmp:/data/rental-notice.sqlite|' /etc/php/8.2/fpm/pool.d/www.conf`. This way we can access the sqlite database file.

# User Types

-   `Admin` - This person can do anything related to the account they are associated with.
-   `Contributor` - This A person can do anything in the account except for billing and managing users (This is not implemented yet).
-   `Super Admin` - Only for our employees, a super admin can access any account across the board.

# SSH Support To Support Servers

We use SSH to connect to support servers. As a result we install ssh keys into our docker container, and need them for local development. For security reasons we leave out `.fly/ssh/id_ed25519` using `.gitignore`. Contact another member of the development team to get this key. This key is injected into the build process via our Ci/CD and stored as a secret at github.

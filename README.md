# About Rental Notice

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

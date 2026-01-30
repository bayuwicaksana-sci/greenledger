# Product Requirements Document - Approval System Laravel 12

---

## Features

- Agnostic, meaning could be applied to any Model.
- Configurable and Customizable, meaning the approval workflow must be able to be configured and customized by the user from frontend.
- Support multi-step approval workflow, parallel and linear.
- Support Conditional Rule for every approval step.
- Auditable.

---

## Supporting Packages

- spatie/laravel-model-states - v2
- spatie/laravel-activitylog - v4
- spatie/laravel-permission - v6
- Any other recommendation package are welcomed

---

## Important Rules

- The implementation of this feature must always follow Laravel 12 Best-Practices, to ensure maintainability and scalability.

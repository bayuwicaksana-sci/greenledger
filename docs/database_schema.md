# Database Schema - Multi-Site Finance & Accounting System
## Table Organization by Dependency Level

Tables are organized from core entities (no dependencies) to increasingly dependent entities, ensuring proper foreign key relationships and migration order.

---

# LEVEL 0: Core Master Tables (No Foreign Key Dependencies)

## 1. sites
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique site identifier |
| site_code | VARCHAR(10) | UNIQUE, NOT NULL | Site code (KLT, MGL) |
| site_name | VARCHAR(100) | NOT NULL | Full site name |
| address | TEXT | NULL | Site address |
| contact_info | VARCHAR(255) | NULL | Contact information |
| is_active | BOOLEAN | DEFAULT TRUE | Active status |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |

**Indexes:** site_code, is_active  
**Dependencies:** None

---

## 2. buyers
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique buyer identifier |
| buyer_name | VARCHAR(200) | NOT NULL | Buyer name |
| contact_person | VARCHAR(100) | NULL | Contact person |
| phone | VARCHAR(20) | NULL | Phone number |
| email | VARCHAR(255) | NULL | Email address |
| address | TEXT | NULL | Buyer address |
| payment_terms | VARCHAR(100) | NULL | Standard payment terms |
| is_active | BOOLEAN | DEFAULT TRUE | Active status |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |

**Indexes:** buyer_name, is_active  
**Dependencies:** None

---

## 3. clients
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique client identifier |
| client_name | VARCHAR(200) | NOT NULL | Client/company name |
| client_type | ENUM | 'SISTER_COMPANY', 'EXTERNAL' | Client category |
| contact_person | VARCHAR(100) | NULL | Contact person |
| phone | VARCHAR(20) | NULL | Phone number |
| email | VARCHAR(255) | NULL | Email address |
| address | TEXT | NULL | Client address |
| tax_id | VARCHAR(50) | NULL | Tax ID / NPWP |
| is_active | BOOLEAN | DEFAULT TRUE | Active status |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |

**Indexes:** client_name, client_type, is_active  
**Dependencies:** None

---

## 4. subsidi_types
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique subsidi type ID |
| type_code | VARCHAR(20) | UNIQUE, NOT NULL | Type code (SSK1, SSK2) |
| type_name | VARCHAR(100) | NOT NULL | Type name |
| description | TEXT | NULL | Type description |
| monthly_pagu | DECIMAL(15,2) | NOT NULL | Monthly allowance |
| is_active | BOOLEAN | DEFAULT TRUE | Active status |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |

**Indexes:** type_code, is_active  
**Dependencies:** None

---

# LEVEL 1: User Management (Depends on sites)

## 5. users
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier |
| email | VARCHAR(255) | UNIQUE, NOT NULL | User email (login) |
| password_hash | VARCHAR(255) | NOT NULL | Hashed password |
| full_name | VARCHAR(100) | NOT NULL | User's full name |
| primary_site_id | BIGINT | FOREIGN KEY → sites(id) | Primary site assignment |
| is_active | BOOLEAN | DEFAULT TRUE | Account active status |
| must_change_password | BOOLEAN | DEFAULT FALSE | Force password change flag |
| last_login_at | TIMESTAMP | NULL | Last successful login |
| last_login_ip | VARCHAR(45) | NULL | Last login IP address |
| failed_login_attempts | INT | DEFAULT 0 | Failed login counter |
| locked_until | TIMESTAMP | NULL | Account lock timestamp |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |
| deleted_at | TIMESTAMP | NULL | Soft delete timestamp |

**Indexes:** email, primary_site_id, is_active, deleted_at  
**Foreign Keys:**
- primary_site_id → sites(id)

---

# LEVEL 2: User Relations & COA (Depends on sites + users)

## 6. user_roles
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique role assignment ID |
| user_id | BIGINT | FOREIGN KEY → users(id) | User reference |
| role | ENUM | 'RO', 'RA', 'MANAGER', 'FINANCE_OP', 'FARM_ADMIN', 'SYSTEM_ADMIN' | Role type |
| assigned_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Role assignment time |
| assigned_by | BIGINT | FOREIGN KEY → users(id) | Who assigned role |

**Indexes:** user_id, role  
**Unique Constraint:** (user_id, role)  
**Foreign Keys:**
- user_id → users(id)
- assigned_by → users(id)

---

## 7. user_site_access
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique access record ID |
| user_id | BIGINT | FOREIGN KEY → users(id) | User reference |
| site_id | BIGINT | FOREIGN KEY → sites(id) | Site reference |
| granted_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Access grant time |
| granted_by | BIGINT | FOREIGN KEY → users(id) | Who granted access |

**Indexes:** user_id, site_id  
**Unique Constraint:** (user_id, site_id)  
**Foreign Keys:**
- user_id → users(id)
- site_id → sites(id)
- granted_by → users(id)

---

## 8. coa_accounts
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique account identifier |
| site_id | BIGINT | FOREIGN KEY → sites(id) | Site reference |
| account_code | VARCHAR(50) | NOT NULL | Full account code (KLT-4-1100) |
| account_name | VARCHAR(200) | NOT NULL | Account full name |
| short_description | TEXT | NULL | Account description |
| account_type | ENUM | 'REVENUE', 'EXPENSE' | Account category |
| parent_account_id | BIGINT | FOREIGN KEY → coa_accounts(id) | Parent for hierarchy |
| hierarchy_level | INT | NOT NULL | Level in hierarchy (1-3) |
| is_active | BOOLEAN | DEFAULT TRUE | Active status |
| first_transaction_at | TIMESTAMP | NULL | First usage (locks code) |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |
| created_by | BIGINT | FOREIGN KEY → users(id) | User who created |
| updated_by | BIGINT | FOREIGN KEY → users(id) | User who last updated |

**Indexes:** site_id, account_code, account_type, is_active, parent_account_id  
**Unique Constraint:** (site_id, account_code)  
**Foreign Keys:**
- site_id → sites(id)
- parent_account_id → coa_accounts(id) [self-referential]
- created_by → users(id)
- updated_by → users(id)

---

## 9. system_settings
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique setting ID |
| setting_key | VARCHAR(100) | UNIQUE, NOT NULL | Setting identifier |
| setting_value | TEXT | NOT NULL | Setting value (JSON for complex) |
| setting_type | VARCHAR(50) | NOT NULL | Data type hint |
| description | TEXT | NULL | Setting description |
| is_system | BOOLEAN | DEFAULT FALSE | System setting (not user-editable) |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |
| updated_by | BIGINT | FOREIGN KEY → users(id) | Who updated |

**Indexes:** setting_key, is_system  
**Foreign Keys:**
- updated_by → users(id)

---

# LEVEL 3: Programs & Subsidi (Depends on sites + users + subsidi_types)

## 10. programs
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique program identifier |
| site_id | BIGINT | FOREIGN KEY → sites(id), NOT NULL | Site reference (immutable) |
| program_code | VARCHAR(50) | UNIQUE, NOT NULL | Program code |
| program_name | VARCHAR(200) | NOT NULL | Program name |
| description | TEXT | NULL | Program description |
| fiscal_year | INT | NOT NULL | Fiscal year (2026) |
| status | ENUM | 'DRAFT', 'ACTIVE', 'COMPLETED', 'ARCHIVED' | Program status |
| total_budget | DECIMAL(15,2) | NOT NULL | Total allocated budget |
| start_date | DATE | NULL | Planned start date |
| end_date | DATE | NULL | Planned end date |
| actual_start_date | DATE | NULL | Actual start date |
| actual_end_date | DATE | NULL | Actual completion date |
| completion_reason | TEXT | NULL | Completion notes |
| research_report_url | VARCHAR(500) | NULL | Final report document |
| is_archived | BOOLEAN | DEFAULT FALSE | Archive status |
| archived_at | TIMESTAMP | NULL | Archive timestamp |
| archived_by | BIGINT | FOREIGN KEY → users(id) | Who archived |
| archive_reason | TEXT | NULL | Reason for archiving |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |
| created_by | BIGINT | FOREIGN KEY → users(id) | User who created |
| updated_by | BIGINT | FOREIGN KEY → users(id) | User who last updated |
| approved_by | BIGINT | FOREIGN KEY → users(id) | AVP who approved |
| approved_at | TIMESTAMP | NULL | Approval timestamp |
| completed_by | BIGINT | FOREIGN KEY → users(id) | Manager who marked complete |
| completed_at | TIMESTAMP | NULL | Completion timestamp |

**Indexes:** site_id, program_code, fiscal_year, status, is_archived, created_by  
**Foreign Keys:**
- site_id → sites(id)
- archived_by → users(id)
- created_by → users(id)
- updated_by → users(id)
- approved_by → users(id)
- completed_by → users(id)

**Check Constraints:**
- total_budget > 0
- CHECK (actual_end_date IS NULL OR actual_end_date >= actual_start_date)

---

## 11. subsidi_eligibility
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique eligibility record |
| subsidi_type_id | BIGINT | FOREIGN KEY → subsidi_types(id), NOT NULL | Subsidi type reference |
| user_id | BIGINT | FOREIGN KEY → users(id), NOT NULL | User reference |
| effective_from | DATE | NOT NULL | Start date |
| effective_to | DATE | NULL | End date (NULL = ongoing) |
| is_active | BOOLEAN | DEFAULT TRUE | Active status |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| created_by | BIGINT | FOREIGN KEY → users(id) | Who created |

**Indexes:** subsidi_type_id, user_id, effective_from, is_active  
**Unique Constraint:** (subsidi_type_id, user_id, effective_from)  
**Foreign Keys:**
- subsidi_type_id → subsidi_types(id)
- user_id → users(id)
- created_by → users(id)

---

## 12. subsidi_claims
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique claim identifier |
| claim_number | VARCHAR(50) | UNIQUE, NOT NULL | Auto-generated claim # |
| user_id | BIGINT | FOREIGN KEY → users(id), NOT NULL | Claimant |
| subsidi_type_id | BIGINT | FOREIGN KEY → subsidi_types(id), NOT NULL | Subsidi type reference |
| claim_month | DATE | NOT NULL | Month (first day of month) |
| claim_amount | DECIMAL(15,2) | NOT NULL | Claimed amount |
| evidence_url | VARCHAR(500) | NULL | Supporting document |
| status | ENUM | 'DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED', 'PAID' | Claim status |
| submitted_at | TIMESTAMP | NULL | Submission timestamp |
| approved_at | TIMESTAMP | NULL | Approval timestamp |
| approved_by | BIGINT | FOREIGN KEY → users(id) | Who approved |
| rejection_reason | TEXT | NULL | Rejection notes |
| paid_at | TIMESTAMP | NULL | Payment timestamp |
| paid_by | BIGINT | FOREIGN KEY → users(id) | Who processed payment |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |

**Indexes:** user_id, subsidi_type_id, claim_month, status, claim_number  
**Foreign Keys:**
- user_id → users(id)
- subsidi_type_id → subsidi_types(id)
- approved_by → users(id)
- paid_by → users(id)

---

# LEVEL 4: Program Details & Transactions (Depends on programs)

## 13. program_assignments
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique assignment ID |
| program_id | BIGINT | FOREIGN KEY → programs(id) | Program reference |
| user_id | BIGINT | FOREIGN KEY → users(id) | User reference |
| role_in_program | ENUM | 'LEAD', 'MEMBER' | Role type |
| assigned_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Assignment time |
| assigned_by | BIGINT | FOREIGN KEY → users(id) | Who assigned |
| removed_at | TIMESTAMP | NULL | Removal timestamp |
| removed_by | BIGINT | FOREIGN KEY → users(id) | Who removed |

**Indexes:** program_id, user_id, role_in_program  
**Unique Constraint:** (program_id, user_id, removed_at) where removed_at IS NULL  
**Foreign Keys:**
- program_id → programs(id)
- user_id → users(id)
- assigned_by → users(id)
- removed_by → users(id)

---

## 14. activities
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique activity identifier |
| program_id | BIGINT | FOREIGN KEY → programs(id), NOT NULL | Program reference |
| activity_name | VARCHAR(200) | NOT NULL | Activity name |
| description | TEXT | NULL | Activity description |
| budget_allocation | DECIMAL(15,2) | NOT NULL | Budget for this activity |
| planned_start_date | DATE | NULL | Planned start |
| planned_end_date | DATE | NULL | Planned end |
| actual_start_date | DATE | NULL | Actual start |
| actual_end_date | DATE | NULL | Actual end |
| status | ENUM | 'PLANNED', 'ACTIVE', 'COMPLETED', 'CANCELLED' | Activity status |
| sort_order | INT | DEFAULT 0 | Display order |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |
| created_by | BIGINT | FOREIGN KEY → users(id) | User who created |
| updated_by | BIGINT | FOREIGN KEY → users(id) | User who last updated |

**Indexes:** program_id, status, sort_order  
**Foreign Keys:**
- program_id → programs(id)
- created_by → users(id)
- updated_by → users(id)

**Check Constraints:**
- budget_allocation >= 0
- CHECK (actual_end_date IS NULL OR actual_end_date >= actual_start_date)

---

## 15. payment_requests
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique request identifier |
| request_number | VARCHAR(50) | UNIQUE, NOT NULL | Auto-generated request # |
| site_id | BIGINT | FOREIGN KEY → sites(id), NOT NULL | Site reference |
| request_date | DATE | NOT NULL | Request date |
| total_amount | DECIMAL(15,2) | NOT NULL | Total request amount |
| purpose | TEXT | NOT NULL | Payment purpose |
| vendor_name | VARCHAR(200) | NULL | Vendor/supplier name |
| is_multi_program | BOOLEAN | DEFAULT FALSE | Multi-program split flag |
| quotation_url | VARCHAR(500) | NULL | Supporting document |
| status | ENUM | 'DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED', 'PAID', 'SETTLED', 'CANCELLED' | Request status |
| batch_time | ENUM | 'MORNING', 'AFTERNOON' | Processing batch |
| submitted_at | TIMESTAMP | NULL | Submission timestamp |
| approved_at | TIMESTAMP | NULL | Approval timestamp |
| approved_by | BIGINT | FOREIGN KEY → users(id) | Manager who approved |
| rejected_at | TIMESTAMP | NULL | Rejection timestamp |
| rejected_by | BIGINT | FOREIGN KEY → users(id) | Who rejected |
| rejection_reason | TEXT | NULL | Rejection notes |
| paid_at | TIMESTAMP | NULL | Payment timestamp |
| paid_by | BIGINT | FOREIGN KEY → users(id) | Finance who processed |
| payment_reference | VARCHAR(100) | NULL | Bank reference |
| settlement_deadline | TIMESTAMP | NULL | 48-hour deadline |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |
| created_by | BIGINT | FOREIGN KEY → users(id) | Requester |

**Indexes:** site_id, request_number, status, batch_time, created_by, submitted_at, settlement_deadline  
**Foreign Keys:**
- site_id → sites(id)
- approved_by → users(id)
- rejected_by → users(id)
- paid_by → users(id)
- created_by → users(id)

**Check Constraints:**
- total_amount > 0

---

## 16. revenue_harvest
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique harvest record ID |
| site_id | BIGINT | FOREIGN KEY → sites(id), NOT NULL | Site reference |
| program_id | BIGINT | FOREIGN KEY → programs(id), NOT NULL | Program reference |
| harvest_number | VARCHAR(50) | NOT NULL | Auto-generated harvest # |
| harvest_date | DATE | NOT NULL | Harvest date |
| harvest_cycle | INT | NOT NULL | Cycle number for program |
| crop_type | VARCHAR(100) | NOT NULL | Crop name |
| quantity_kg | DECIMAL(10,2) | NOT NULL | Quantity harvested (kg) |
| price_per_kg | DECIMAL(10,2) | NOT NULL | Price per kilogram |
| total_revenue | DECIMAL(15,2) | NOT NULL | Total revenue amount |
| buyer_id | BIGINT | FOREIGN KEY → buyers(id), NOT NULL | Buyer reference |
| payment_method | ENUM | 'CASH', 'BANK_TRANSFER' | Payment type |
| payment_date | DATE | NOT NULL | Payment received date |
| bank_reference | VARCHAR(100) | NULL | Bank transaction reference |
| coa_account_id | BIGINT | FOREIGN KEY → coa_accounts(id), NOT NULL | Revenue COA account |
| notes | TEXT | NULL | Additional notes |
| status | ENUM | 'POSTED', 'UNDER_REVIEW', 'CORRECTED' | Record status |
| reviewed_at | TIMESTAMP | NULL | Manager review timestamp |
| reviewed_by | BIGINT | FOREIGN KEY → users(id) | Manager who reviewed |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |
| created_by | BIGINT | FOREIGN KEY → users(id) | RO who recorded |
| corrected_by | BIGINT | FOREIGN KEY → users(id) | Who corrected |
| correction_reason | TEXT | NULL | Reason for correction |

**Indexes:** site_id, program_id, harvest_date, buyer_id, coa_account_id, created_by, harvest_number  
**Foreign Keys:**
- site_id → sites(id)
- program_id → programs(id)
- buyer_id → buyers(id)
- coa_account_id → coa_accounts(id)
- reviewed_by → users(id)
- created_by → users(id)
- corrected_by → users(id)

**Check Constraints:**
- quantity_kg > 0
- price_per_kg > 0
- total_revenue = quantity_kg * price_per_kg

---

## 17. revenue_testing_services
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique service record ID |
| site_id | BIGINT | FOREIGN KEY → sites(id), NOT NULL | Site reference |
| program_id | BIGINT | FOREIGN KEY → programs(id), NULL | Related program (optional) |
| service_number | VARCHAR(50) | UNIQUE, NOT NULL | Auto-generated service # |
| client_id | BIGINT | FOREIGN KEY → clients(id), NOT NULL | Client reference |
| client_type | ENUM | 'SISTER_COMPANY', 'EXTERNAL' | Client category |
| service_type | VARCHAR(100) | NOT NULL | Type of testing service |
| service_description | TEXT | NOT NULL | Detailed description |
| contract_value | DECIMAL(15,2) | NOT NULL | Service contract value |
| start_date | DATE | NOT NULL | Service start date |
| completion_date | DATE | NULL | Service completion date |
| payment_received_date | DATE | NULL | Payment date |
| payment_status | ENUM | 'PENDING', 'RECEIVED' | Payment status |
| coa_account_id | BIGINT | FOREIGN KEY → coa_accounts(id), NOT NULL | Revenue COA account |
| contract_url | VARCHAR(500) | NULL | Contract document |
| status | ENUM | 'DRAFT', 'SUBMITTED', 'APPROVED', 'COMPLETED' | Service status |
| submitted_at | TIMESTAMP | NULL | Submission timestamp |
| approved_at | TIMESTAMP | NULL | Approval timestamp |
| approved_by | BIGINT | FOREIGN KEY → users(id) | Manager who approved |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |
| created_by | BIGINT | FOREIGN KEY → users(id) | RA who recorded |

**Indexes:** site_id, program_id, client_id, service_number, payment_status, created_by  
**Foreign Keys:**
- site_id → sites(id)
- program_id → programs(id)
- client_id → clients(id)
- coa_account_id → coa_accounts(id)
- approved_by → users(id)
- created_by → users(id)

**Check Constraints:**
- contract_value > 0

---

# LEVEL 5: Split Transactions & Financials (Depends on payment_requests + activities)

## 18. payment_request_splits
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique split identifier |
| payment_request_id | BIGINT | FOREIGN KEY → payment_requests(id), NOT NULL | Request reference |
| program_id | BIGINT | FOREIGN KEY → programs(id), NOT NULL | Program reference |
| activity_id | BIGINT | FOREIGN KEY → activities(id), NOT NULL | Activity reference |
| coa_account_id | BIGINT | FOREIGN KEY → coa_accounts(id), NOT NULL | COA account |
| split_amount | DECIMAL(15,2) | NOT NULL | Amount for this split |
| split_percentage | DECIMAL(5,2) | NOT NULL | Percentage of total |
| notes | TEXT | NULL | Split-specific notes |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |

**Indexes:** payment_request_id, program_id, activity_id, coa_account_id  
**Foreign Keys:**
- payment_request_id → payment_requests(id)
- program_id → programs(id)
- activity_id → activities(id)
- coa_account_id → coa_accounts(id)

**Check Constraints:**
- split_amount > 0
- split_percentage >= 0 AND split_percentage <= 100

---

## 19. settlements
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique settlement identifier |
| payment_request_id | BIGINT | FOREIGN KEY → payment_requests(id), UNIQUE, NOT NULL | Request reference |
| actual_amount | DECIMAL(15,2) | NOT NULL | Actual spent amount |
| surplus_amount | DECIMAL(15,2) | NOT NULL | Unspent amount |
| receipt_url | VARCHAR(500) | NULL | Receipt document |
| settlement_notes | TEXT | NULL | Settlement description |
| submitted_at | TIMESTAMP | NULL | Submission timestamp |
| submitted_by | BIGINT | FOREIGN KEY → users(id) | Who submitted |
| status | ENUM | 'PENDING', 'SUBMITTED', 'REVISION_REQUESTED', 'APPROVED', 'REJECTED' | Settlement status |
| reviewed_at | TIMESTAMP | NULL | Review timestamp |
| reviewed_by | BIGINT | FOREIGN KEY → users(id) | Finance who reviewed |
| review_notes | TEXT | NULL | Review comments |
| approved_at | TIMESTAMP | NULL | Final approval timestamp |
| approved_by | BIGINT | FOREIGN KEY → users(id) | Who approved |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last update time |

**Indexes:** payment_request_id, status, submitted_at, reviewed_by  
**Foreign Keys:**
- payment_request_id → payment_requests(id)
- submitted_by → users(id)
- reviewed_by → users(id)
- approved_by → users(id)

**Check Constraints:**
- actual_amount >= 0
- surplus_amount >= 0

---

## 20. program_financials
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique record ID |
| program_id | BIGINT | FOREIGN KEY → programs(id), UNIQUE, NOT NULL | Program reference |
| total_budget | DECIMAL(15,2) | NOT NULL | Allocated budget |
| total_spent | DECIMAL(15,2) | NOT NULL | Total expenses |
| total_revenue | DECIMAL(15,2) | NOT NULL | Total revenue |
| harvest_revenue | DECIMAL(15,2) | NOT NULL | Revenue from harvests |
| testing_revenue | DECIMAL(15,2) | NOT NULL | Revenue from services |
| net_income | DECIMAL(15,2) | NOT NULL | Revenue - Expenses |
| budget_utilization_pct | DECIMAL(5,2) | NOT NULL | Spent/Budget percentage |
| available_budget | DECIMAL(15,2) | NOT NULL | Budget - Spent |
| pending_settlements | DECIMAL(15,2) | NOT NULL | Unsettled amounts |
| last_transaction_at | TIMESTAMP | NULL | Last financial activity |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last recalculation |

**Indexes:** program_id, last_transaction_at  
**Foreign Keys:**
- program_id → programs(id)

**Note:** Materialized view, refreshed on transaction changes

---

## 21. activity_financials
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique record ID |
| activity_id | BIGINT | FOREIGN KEY → activities(id), UNIQUE, NOT NULL | Activity reference |
| budget_allocation | DECIMAL(15,2) | NOT NULL | Activity budget |
| total_spent | DECIMAL(15,2) | NOT NULL | Total expenses |
| budget_utilization_pct | DECIMAL(5,2) | NOT NULL | Spent/Budget percentage |
| available_budget | DECIMAL(15,2) | NOT NULL | Budget - Spent |
| transaction_count | INT | NOT NULL | Number of transactions |
| last_transaction_at | TIMESTAMP | NULL | Last financial activity |
| updated_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Last recalculation |

**Indexes:** activity_id, last_transaction_at  
**Foreign Keys:**
- activity_id → activities(id)

**Note:** Materialized view, refreshed on transaction changes

---

# LEVEL 6: Settlement Details (Depends on settlements + payment_request_splits)

## 22. settlement_splits
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique split identifier |
| settlement_id | BIGINT | FOREIGN KEY → settlements(id), NOT NULL | Settlement reference |
| payment_split_id | BIGINT | FOREIGN KEY → payment_request_splits(id), NOT NULL | Original split reference |
| actual_split_amount | DECIMAL(15,2) | NOT NULL | Actual amount for split |
| surplus_split_amount | DECIMAL(15,2) | NOT NULL | Surplus for this split |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record creation time |

**Indexes:** settlement_id, payment_split_id  
**Foreign Keys:**
- settlement_id → settlements(id)
- payment_split_id → payment_request_splits(id)

---

# LEVEL 7: Audit & System Tables (Can reference any entity)

## 23. audit_logs
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique log identifier |
| user_id | BIGINT | FOREIGN KEY → users(id), NULL | User who performed action |
| action_type | VARCHAR(50) | NOT NULL | Action category (CREATE, UPDATE, DELETE, APPROVE, etc.) |
| entity_type | VARCHAR(50) | NOT NULL | Entity affected (PROGRAM, PAYMENT, etc.) |
| entity_id | BIGINT | NULL | ID of affected entity |
| site_id | BIGINT | FOREIGN KEY → sites(id), NULL | Related site |
| before_data | JSON | NULL | State before change |
| after_data | JSON | NULL | State after change |
| ip_address | VARCHAR(45) | NULL | User's IP address |
| user_agent | VARCHAR(255) | NULL | Browser/client info |
| description | TEXT | NULL | Human-readable description |
| is_admin_action | BOOLEAN | DEFAULT FALSE | Admin override flag |
| is_critical | BOOLEAN | DEFAULT FALSE | Critical action flag |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Action timestamp |

**Indexes:** user_id, action_type, entity_type, entity_id, site_id, created_at, is_admin_action, is_critical  
**Foreign Keys:**
- user_id → users(id) [ON DELETE SET NULL]
- site_id → sites(id)

**Partitioning:** Monthly by created_at

---

## 24. digital_signatures
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique signature identifier |
| user_id | BIGINT | FOREIGN KEY → users(id), NOT NULL | Signer reference |
| entity_type | VARCHAR(50) | NOT NULL | Entity type (PAYMENT_REQUEST, BUDGET, etc.) |
| entity_id | BIGINT | NOT NULL | Entity ID |
| signature_hash | VARCHAR(255) | NOT NULL | Digital signature hash |
| signature_data | TEXT | NOT NULL | Signature metadata (JSON) |
| signed_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Signature timestamp |
| ip_address | VARCHAR(45) | NOT NULL | Signer's IP |

**Indexes:** user_id, entity_type, entity_id, signed_at  
**Unique Constraint:** (entity_type, entity_id, user_id)  
**Foreign Keys:**
- user_id → users(id)

---

## 25. notifications
| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Unique notification ID |
| user_id | BIGINT | FOREIGN KEY → users(id), NOT NULL | Recipient |
| notification_type | VARCHAR(50) | NOT NULL | Type (APPROVAL_REQUEST, SETTLEMENT_DUE, etc.) |
| title | VARCHAR(255) | NOT NULL | Notification title |
| message | TEXT | NOT NULL | Notification body |
| entity_type | VARCHAR(50) | NULL | Related entity type |
| entity_id | BIGINT | NULL | Related entity ID |
| priority | ENUM | 'LOW', 'MEDIUM', 'HIGH', 'URGENT' | Priority level |
| is_read | BOOLEAN | DEFAULT FALSE | Read status |
| read_at | TIMESTAMP | NULL | Read timestamp |
| is_sent_email | BOOLEAN | DEFAULT FALSE | Email sent flag |
| email_sent_at | TIMESTAMP | NULL | Email sent timestamp |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Notification created time |

**Indexes:** user_id, is_read, notification_type, priority, created_at, entity_type, entity_id  
**Foreign Keys:**
- user_id → users(id)

**Partitioning:** Monthly by created_at

---

# Additional Composite Indexes for Performance

## Recommended Composite Indexes

**programs:**
- (site_id, fiscal_year, status)
- (site_id, is_archived, status)
- (fiscal_year, status, is_archived)

**payment_requests:**
- (site_id, status, created_at)
- (created_by, status, created_at)
- (status, settlement_deadline)

**payment_request_splits:**
- (program_id, activity_id, created_at)
- (activity_id, program_id)

**revenue_harvest:**
- (program_id, harvest_date, harvest_cycle)
- (site_id, harvest_date)

**audit_logs:**
- (user_id, created_at, action_type)
- (entity_type, entity_id, created_at)

---

# Database Constraints Summary

## Foreign Key Constraints
All foreign keys should have:
- **ON DELETE RESTRICT** (prevent deletion of referenced records)
- **ON UPDATE CASCADE** (update references if parent ID changes)
- **Exception:** audit_logs.user_id can be ON DELETE SET NULL

## Triggers (Business Logic Enforcement)

1. **payment_request_splits_sum_trigger**: Validate sum of splits equals total_amount
2. **budget_availability_trigger**: Check available budget before payment approval
3. **activity_budget_sum_trigger**: Validate sum of activity budgets <= program total
4. **settlement_deadline_trigger**: Auto-calculate deadline as paid_at + 48 hours
5. **program_financials_refresh_trigger**: Update materialized views on transactions
6. **audit_log_trigger**: Auto-log changes to critical tables

---

# Data Retention & Archival Strategy

## Hot Data (Active Database)
- Current fiscal year + 2 previous years
- All active/completed programs
- Recent 12 months of audit logs

## Warm Storage (Archive Database)
- Fiscal years 3-5 years old
- Archived programs
- Audit logs 1-5 years old

## Cold Storage (Compressed/Backup)
- Fiscal years > 5 years old
- Historical archived programs
- Long-term compliance data

## Partitioning Strategy
- **audit_logs**: Monthly partitioning by created_at
- **notifications**: Monthly partitioning by created_at
- **payment_requests**: Yearly partitioning by request_date
- **revenue_harvest**: Yearly partitioning by harvest_date

---

# Security Considerations

1. **Row-Level Security (RLS)**: Implement site_id filtering based on user permissions
2. **Encryption**: Encrypt sensitive fields (password_hash, signature_hash)
3. **Audit Everything**: All INSERT/UPDATE/DELETE logged to audit_logs
4. **No Hard Deletes**: Use soft deletes (deleted_at) for all user data
5. **Rate Limiting**: Track failed_login_attempts, implement account locking
6. **Data Masking**: Mask sensitive data in non-production environments

---

# Scalability Considerations

1. **Read Replicas**: Use for reporting queries, dashboard data
2. **Caching Layer**: Redis for program_financials, frequently accessed COA
3. **Connection Pooling**: PgBouncer or similar for connection management
4. **Batch Processing**: Queue-based for notification sending, report generation
5. **Materialized Views**: Refresh program_financials and activity_financials asynchronously
6. **Archive Strategy**: Regular archival of old fiscal years to maintain performance

---

# Database Migration Order

When creating the database, follow this table creation order:

**Level 0:** sites, buyers, clients, subsidi_types  
**Level 1:** users  
**Level 2:** user_roles, user_site_access, coa_accounts, system_settings  
**Level 3:** programs, subsidi_eligibility, subsidi_claims  
**Level 4:** program_assignments, activities, payment_requests, revenue_harvest, revenue_testing_services  
**Level 5:** payment_request_splits, settlements, program_financials, activity_financials  
**Level 6:** settlement_splits  
**Level 7:** audit_logs, digital_signatures, notifications  

---

**Total Tables: 25**  
**Estimated Size (3 years):** ~50-100GB with proper archival  
**Expected Performance:** Sub-second queries with proper indexing for 50+ concurrent users

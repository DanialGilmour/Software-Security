# Secure Student Portal - Full System Documentation
**Course:** CCSB5113 - Software Security
**Framework:** Vanilla PHP + MySQLi (WAMP Environment)

---

## 1. Project Requirements & Objective
The objective is to develop a **Secure Student Information System** that prioritizes data integrity and access control over complex design. The system follows the **Secured Software Development Life Cycle (SSDLC)** to mitigate common web threats.

### Functional Requirements:
- **Authentication**: Secure login for multiple user roles.
- **Role-Based Access Control (RBAC)**: Distinct permissions for administrative and student users.
- **Vertical Access Control**: Hardened access levels based on hierarchical ID parity.
- **Course Management**: Admins can manage the subject catalog.
- **Registration**: Students can enroll and drop subjects.
- **Grading**: Administrative staff can assign academic grades.
- **Audit Logging**: Mandatory tracking of all system activity.

### Security Requirements:
- **Hashing**: All passwords must be stored using the Bcrypt algorithm.
- **Injection Protection**: All database queries must use Prepared Statements.
- **Session Security**: Enforce session-based authentication to prevent unauthorized URL access.
- **ID Parity**: Use specific last-digit identifiers (2, 4, 6, 8, 1) for distinct roles.

---

## 2. Security Architecture

### 🛡️ Password Security (Bcrypt)
The system uses `password_hash()` with the `PASSWORD_BCRYPT` constant. 
- **Cost Factor**: 10 rounds of hashing.
- **Salt**: Automatically generated and included in the hash.

### 🛡️ Database Protection
Integrated with `mysqli` using **Parameterized Queries**. 
Example: `mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?")`.

---

## 3. Vertical Access Control (VAC) Logic
The system enforces access control based on the **last digit of the ID**. 

### Role Hierarchy & Dummy Data:
| Role | Last Digit | Permissions | Dummy User Email | Initial ID |
| :--- | :---: | :--- | :--- | :---: |
| **Registrar** | **2** | **Full Access** | `registrar@portal.edu` | 2 |
| **Clerk** | **4** | **Management** | `clerk@portal.edu` | 4 |
| **Lecturer** | **6** | **View Only** | `lecturer@portal.edu` | 6 |
| **Maintenance** | **8** | **Logs Only** | `tech@portal.edu` | 8 |
| **Student** | **1** | **Portal Access** | `student@portal.edu` | 1 |

---

## 4. Pre-Seeded Dummy User Accounts
The following accounts are pre-loaded into `schema.sql` for testing:

### Administrative Roles (Even IDs)
1. **Registrar (ID: 2)**: Assoc. Prof. Ts. Dr. Danial Rosli (`registrar@portal.edu`)
2. **Registrar (ID: 12)**: Dr. Hani Ramadhani (`registrar2@portal.edu`)
3. **Clerk (ID: 4)**: Encik Adam Smith (`clerk@portal.edu`)
4. **Clerk (ID: 14)**: Puan Siti Aminah (`clerk2@portal.edu`)
5. **Lecturer (ID: 6)**: Prof. Lee Wei (`lecturer@portal.edu`)
6. **Lecturer (ID: 16)**: Dr. Tan Kian (`lecturer2@portal.edu`)
7. **Maintenance (ID: 8)**: Tech Support (`tech@portal.edu`)

### Student Roles (Odd IDs)
1. **Student (ID: 1)**: Danial Hensem (`student@portal.edu`)
2. **Student (ID: 11)**: Ali Bin Abu (`student2@portal.edu`)

**Default Password for all dummy users:** `Student@123!` (or `Admin@123!` for Registrar).

---

## 5. Deployment Instructions
1. Import `schema.sql` into a database named `student_portal`.
2. Place all PHP files in `C:\wamp64\www\Software-Security\`.
3. Open `http://localhost/Software-Security/` in your browser.

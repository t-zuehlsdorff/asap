# ASAP
"A Scout And Patcher" aims to scout new versions of software stored in the FreeBSD portstree and automatically tries to create and verify a patch to update its version in the portstree.

# Requirements
Needed ports:
- www/dddbl

Needed software:
- PHP 5.6 +
- PostgreSQL 9.5 +

Needed PHP modules:
- pdo_pgsql

# Additional requirements for tests

Needed PHP modules:
- pcntl
- posix
- sysvmsg
- sysvshm

How to execute the tests:
php third_party/aphpunit/aphpunit.php tests/

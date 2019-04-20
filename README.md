.checkout
=========

A Symfony project created on November 27, 2018, 10:29 am.

# Fitness Manager

### About

This is the project for my final exam on “PHP MVC Frameworks - Symfony” course at SoftUni. It is a Fitness Manager. The whole project is created using Symfony's components, Doctrine and Twig. Also using base Bootstrap and MySQL as database.  


### Features

- SuperAdmin panel
    * Add/Edit/Remove users
    * Add role to users
    * Switch users to Active/Nonactive
- Receptionist panel
    * Add/Edit/Remove users
    * Add only client or trainer role to users
    * Add activities
        * Add/Edit/Delete activities
        * Add/Remove trainer to activity
        * Add/Remove client to activity
    * Add card to users
         * Add/Edit/Delete/Remove cards from users
    * Add orders to cards
         * Add/Edit/Delete/Remove orders from cards
    * Check card
- Client panel
    * Add card
         * Add/Edit/Delete/Remove cards from users
    * Add orders
         * Add/Edit/Delete/Remove orders from cards

### How to run it

First of all its Symfony project, so you can do every requirement to run a Symfony's project (install/update composer, create a database, update the schema and so on...).
    - you can use the terminal: 
        - php bin/console doctrine:database:create
        - php bin/console doctrine:schema:update --dump-sql
        - php bin/console doctrine:schema:update --force
        - php bin/console server:run
        
Second enter 'ROLE_SUPER_ADMIN', 'ROLE_RECEPTIONIST', 'ROLE_CLIENT', 'ROLE_TRAINER' in table 'role'
    OR run file -> db.sql

### How it works

You have to register 'ROLE_SUPER_ADMIN'. If the first user is not a SUPER_ADMIN, then other users will not be able to register. Only SUPER_ADMIN can add ROLE to users.
Only 'ROLE_RECEPTIONIST' have rights to check cards and etc., to add sports classes (activities), to add trainers and clients to activities. 
'ROLE_TRAINERS' and 'ROLE_CLIENTS' have limited rights.
A user with a 'ROLE_TRAINERS' can be a trainer in several classes and can also be a client in others.
A user with a 'ROLE_CLIENTS' can be a client in several classes.
Classes have capacity limits.

### 



# Client-Contact Management System

## Overview of the app

this system is a min CRM 

### what does it do 

It tracks :
- clients 
- contacts 
- links between them
---

## actors 
### Primary actor

- **Admin**

### Responsibilities: 
- create clients
- create contacts 
- link and unlink them 
- view them and their details and relationships


---

## Use cases

### clients

- create a client
- view a client
- view clients
- link contact to client
- unlink contact 

### contacts 
- create a contact
- view a contact
- view contacts
- link client to contact
- unlink client

--

## D model

### Entities 

### client 
- id 
- name
- client_code(unique)

### contact 
- id 
- name
- surname
- email (unique)

### ClientContact
- client_id
- contact_id


### Relationship

this is a many to many 
----

# Project structure


```
/models
  Client.php
  Contact.php
  ClientContact.php

/controllers
  ClientController.php
  ContactController.php

/views
  /clients
  /contacts
```
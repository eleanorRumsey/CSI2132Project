CREATE TABLE rules
(rule_id SERIAL, 
rule_type VARCHAR(10) NOT NULL,
PRIMARY KEY (rule_id));

CREATE TABLE payment_type
(payment_type_id SERIAL,
payment_type VARCHAR(10) NOT NULL,
PRIMARY KEY(payment_type_id));

CREATE TABLE address_type
(address_type_id SERIAL, 
address_type VARCHAR(18) NOT NULL,
PRIMARY KEY(address_type_id));

CREATE TABLE person_name
(name_id SERIAL, 
first_name VARCHAR(50) NOT NULL,
middle_name VARCHAR(50),
last_name VARCHAR(50) NOT NULL,
PRIMARY KEY(name_id));

CREATE TABLE amenity
(amenity_id SERIAL, 
amenity_type VARCHAR(10) NOT NULL,
PRIMARY KEY(amenity_id));

CREATE TABLE company_position 
(position_id SERIAL, 
position_type VARCHAR(16) NOT NULL,
PRIMARY KEY (position_id));

CREATE TABLE property_type
(property_type_id SERIAL, 
property_type VARCHAR(9) NOT NULL,
PRIMARY KEY (property_type_id));

CREATE TABLE room_type
(room_type_id SERIAL, 
room_type VARCHAR (15) NOT NULL,
PRIMARY KEY (room_type_id));

CREATE TABLE address
(address_id SERIAL,
postal_code VARCHAR(6) NOT NULL,
address_type_id INT NOT NULL,
street_number INT NOT NULL,
unit INT,
street_name VARCHAR(20) NOT NULL,
city VARCHAR(20) NOT NULL,
province VARCHAR(2) NOT NULL,
country VARCHAR(56) NOT NULL,
PRIMARY KEY (address_id),
FOREIGN KEY (address_type_id) REFERENCES address_type (address_type_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
CHECK (province IN ('NS', 'NB', 'PE', 'NL', 'QC', 'ON', 'MB', 
					'SK', 'AB', 'BC', 'YK', 'NT', 'NU')));

CREATE TABLE host 
(host_id SERIAL, 
address_id INT NOT NULL, 
name_id INT NOT NULL, 
email VARCHAR(100) NOT NULL,
phone_number INT NOT NULL, 
active VARCHAR(1),
PRIMARY KEY (host_id),
FOREIGN KEY (address_id) REFERENCES address (address_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
FOREIGN KEY (name_id) REFERENCES person_name (name_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
CHECK (active IN('Y','N')),
CHECK (email LIKE '%_@__%.__%'));

CREATE TABLE guest
(guest_id SERIAL, 
address_id INT NOT NULL, 
name_id INT NOT NULL, 
email VARCHAR(100) NOT NULL,
phone_number INT NOT NULL,
PRIMARY KEY (guest_id),
FOREIGN KEY (address_id) REFERENCES address (address_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
FOREIGN KEY (name_ID) REFERENCES person_name (name_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
CHECK (email LIKE '%_@__%.__%'));

CREATE TABLE payment
(payment_id SERIAL, 
host_id INT NOT NULL, 
guest_id INT NOT NULL, 
payment_type_id INT NOT NULL,
amount NUMERIC(6, 2) NOT NULL,
status VARCHAR(9) NOT NULL,
PRIMARY KEY(payment_id),
FOREIGN KEY (host_id) REFERENCES host (host_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
FOREIGN KEY (guest_id) REFERENCES guest (guest_id) 
 ON DELETE SET NULL ON UPDATE CASCADE,
FOREIGN KEY (payment_type_id) REFERENCES payment_type (payment_type_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
CHECK (status IN ('Approved','Completed','Pending','Declined')));

CREATE TABLE property
(property_id SERIAL, 
host_id INT NOT NULL, 
property_type_id INT NOT NULL,
room_type_id INT NOT NULL, 
bed_setup_id INT NOT NULL, 
address_id INT NOT NULL, 
guest_capacity INT NOT NULL, 
num_bathrooms INT NOT NULL, 
num_bedrooms INT NOT NULL, 
next_available_date DATE NOT NULL,
description VARCHAR(2000) NOT NULL,
rate NUMERIC (4, 2) NOT NULL, 
active VARCHAR(1) NOT NULL,
image VARCHAR(20) NOT NULL,
PRIMARY KEY (property_id),
FOREIGN KEY (host_id) REFERENCES host (host_id) 
 ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (property_type_id) REFERENCES property_type (property_type_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
FOREIGN KEY (room_type_id) REFERENCES room_type (room_type_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
FOREIGN KEY (address_id) REFERENCES address (address_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
CHECK (active IN ('Y', 'N')));

CREATE TABLE bed_setup
(bed_setup_id SERIAL, 
property_id INT NOT NULL,
bed_type VARCHAR(5) NOT NULL,
num_of_beds INT NOT NULL,
PRIMARY KEY(bed_setup_id),
FOREIGN KEY (property_id) REFERENCES property (property_id) 
 ON DELETE SET NULL ON UPDATE CASCADE,
CHECK (bed_type IN ('Twin', 'Full', 'Queen', 'King')));

CREATE TABLE review 
(review_id SERIAL, 
guest_id INT NOT NULL,
property_id INT NOT NULL, 
overall_rating INT NOT NULL,
communication_rating INT NOT NULL,
clean_rating INT NOT NULL,
value_rating INT NOT NULL,
PRIMARY KEY (review_id),
FOREIGN KEY (guest_id) REFERENCES guest (guest_id) 
 ON DELETE SET NULL ON UPDATE CASCADE,
FOREIGN KEY (property_id) REFERENCES property (property_id) 
 ON DELETE SET NULL ON UPDATE CASCADE,
CHECK (overall_rating > 0 AND overall_rating <= 5),
CHECK (communication_rating > 0 AND communication_rating <= 5),
CHECK (clean_rating > 0 AND clean_rating <= 5),
CHECK (value_rating > 0 and value_rating <=5));

CREATE TABLE rental_agreement 
(agreement_id SERIAL, 
property_id INT NOT NULL, 
guest_id INT NOT NULL, 
host_id INT NOT NULL, 
document_link VARCHAR(2048) NOT NULL,
signed BOOL NOT NULL,
signing_date DATE NOT NULL,
start_date DATE NOT NULL, 
end_date DATE NOT NULL,
PRIMARY KEY (agreement_id),
FOREIGN KEY (property_id) REFERENCES property (property_id) 
 ON DELETE SET NULL ON UPDATE CASCADE, 
FOREIGN KEY (guest_id) REFERENCES guest (guest_id) 
 ON DELETE SET NULL ON UPDATE CASCADE,
FOREIGN KEY (host_id) REFERENCES host (host_id) 
 ON DELETE SET NULL ON UPDATE CASCADE);

CREATE TABLE branch
(branch_id SERIAL, 
country VARCHAR(56) NOT NULL,
branch_manager INT NOT NULL, 
address_id INT NOT NULL, 
PRIMARY KEY (branch_id),
FOREIGN KEY (address_id) REFERENCES address (address_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE);

CREATE TABLE employee 
(employee_id SERIAL, 
branch_id INT NOT NULL, 
name_id INT NOT NULL, 
hire_date DATE NOT NULL, 
yearly_salary INT NOT NULL, 
position_id INT NOT NULL, 
PRIMARY KEY(employee_id),
FOREIGN KEY (branch_id) REFERENCES branch (branch_id) 
 ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (name_id) REFERENCES person_name (name_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE,
FOREIGN KEY (position_id) REFERENCES company_position (position_id) 
 ON DELETE RESTRICT ON UPDATE CASCADE);
 
ALTER TABLE branch ADD CONSTRAINT branch_manager_fkey
FOREIGN KEY (branch_manager) REFERENCES employee (employee_id) 
ON DELETE RESTRICT ON UPDATE CASCADE;

CREATE TABLE property_rules
(property_rules_id SERIAL,
property_id INT NOT NULL, 
rule_id INT NOT NULL,
PRIMARY KEY (property_rules_id),
FOREIGN KEY (property_id) REFERENCES property (property_id) 
 ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (rule_id) REFERENCES rules (rule_id) 
 ON UPDATE CASCADE ON DELETE CASCADE);

CREATE TABLE property_amenities 
(property_amenity_id SERIAL,
property_id INT NOT NULL, 
amenity_id INT NOT NULL, 
PRIMARY KEY (property_amenity_id),
FOREIGN KEY (property_id) REFERENCES property (property_id) 
 ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (amenity_id) REFERENCES amenity (amenity_id) 
 ON DELETE CASCADE ON UPDATE CASCADE);
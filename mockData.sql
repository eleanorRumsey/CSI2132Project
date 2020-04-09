-- Information for Host 1 --
INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('A1A1A1', 1, 12, NULL, 'Street St.', 'Ottawa', 'ON', 'Canada');

INSERT INTO person_name(first_name, middle_name, last_name) 
VALUES ('Ellie', NULL, 'Rumsey');

INSERT INTO host(address_id, name_id, email, phone_number, active) 
VALUES (1, 1, 'email@gmail.com', '1111111111', 'Y');

-- Properties of Host 1 --
INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('X2P4D9', 2, 17, NULL, 'Pine St.', 'Toronto', 'ON', 'Canada');

INSERT INTO property(property_name, host_id, property_type_id, room_type_id, address_id, guest_capacity, num_bathrooms, 
					 num_bedrooms, next_available_date, description, rate, active,image)
VALUES ('Curvy House', 1, 2, 1, 2, 5, 3, 2, '2020-05-01', 'This is a house', 540.00, 'Y', 'House1.jpg');

INSERT INTO bed_setup(property_id, bed_type, num_of_beds) VALUES (1, 'King', 1), (1, 'Twin', 2);
INSERT INTO property_rules(property_id, rule_id) VALUES (1, 3);
INSERT INTO property_amenities(property_id, amenity_id) VALUES (1, 1), (1, 2), (1, 3), (1, 4), (1, 5);

INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('D2T4E2', 2, 389, 12, 'Apple Blvd.', 'Montreal', 'QC', 'Canada');

INSERT INTO property(property_name, host_id, property_type_id, room_type_id, address_id, guest_capacity, num_bathrooms, 
					 num_bedrooms, next_available_date, description, rate, active,image)
VALUES ('Modern Apartment', 1, 1, 2, 3, 2, 1, 1, '2020-08-14', 'This apartment is in a nice highrise building', 204.79, 'Y', 'Apartment1.jpg');

INSERT INTO bed_setup(property_id, bed_type, num_of_beds) VALUES (2, 'Queen', 1);
INSERT INTO property_rules(property_id, rule_id) VALUES (2, 3), (2, 1);
INSERT INTO property_amenities(property_id, amenity_id) VALUES (2, 3), (2, 4), (2, 5);

--Host 2--
INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('14153', 1, 234, 12, 'Park Place', 'New York', 'NY', 'United States');

INSERT INTO person_name(first_name, middle_name, last_name) 
VALUES ('Calvin', 'Henry', 'Smith');

INSERT INTO host(address_id, name_id, email, phone_number, active) 
VALUES (2, 2, 'calvinsmith@host.com', '2837492348', 'Y');

--Properties of Host 2---
INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('13423', 2, 465, 45, 'Moon Crescent', 'New York', 'NY', 'United States');

INSERT INTO property(property_name, host_id, property_type_id, room_type_id, address_id, guest_capacity, num_bathrooms, 
					 num_bedrooms, next_available_date, description, rate, active,image)
VALUES ('Spacious shared loft', 2, 4, 3, 5, 2, 1, 2, '2020-10-11', 'Renting the spare room in my loft.', 129.00, 'Y', 'Loft1.jpg');

INSERT INTO bed_setup(property_id, bed_type, num_of_beds) VALUES (3, 'Queen', 1);
INSERT INTO property_rules(property_id, rule_id) VALUES (3, 1), (3, 2);
INSERT INTO property_amenities(property_id, amenity_id) VALUES (3, 2), (3, 3), (3, 4), (3, 5), (3, 6), (3, 7);

INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('A1B2C3', 2, 42, 145, 'Road Rd.', 'Montreal', 'QC', 'Canada');

INSERT INTO property(property_name, host_id, property_type_id, room_type_id, address_id, guest_capacity, num_bathrooms, 
					 num_bedrooms, next_available_date, description, rate, active,image)
VALUES ('Rustic Cottage', 2, 3, 1, 6, 6, 3, 3, '2020-04-12', 'A rustic cottage for a scenic getaway.', 234.45, 'Y', 'Cottage1.jpg');

INSERT INTO bed_setup(property_id, bed_type, num_of_beds) VALUES (4, 'Twin', 2), (4, 'Queen', 2);
INSERT INTO property_rules(property_id, rule_id) VALUES (4, 3), (4, 2), (4, 1);
INSERT INTO property_amenities(property_id, amenity_id) VALUES (4, 1), (4, 3), (4, 5);


-- Guest 1 --
INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('H5H3H3', 1, 12, 235, 'Purple St.', 'Ottawa', 'ON', 'Canada');

INSERT INTO person_name(first_name, middle_name, last_name) 
VALUES ('G.', 'NULL', 'Guest');

INSERT INTO guest(address_id, name_id, email, phone_number)
VALUES(7, 3,'guest@gmail.com', '3948203984');

-- Guest 2 --
INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('G0G0G0', 1, 5, 14, 'Red Rd.', 'Moncton', 'NB', 'Canada');

INSERT INTO person_name(first_name, middle_name, last_name) 
VALUES ('Charlie', 'Alpha', 'Omega');

INSERT INTO guest(address_id, name_id, email, phone_number)
VALUES(8, 2,'charlieo@gmail.com', '6133456549');

-- Branches and employees --
WITH a_id AS 
(INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country) 
	VALUES ('W3W5W5', 3, 500, 34, 'Branch St.', 'Ottawa', 'ON', 'Canada') RETURNING address_id)
INSERT INTO branch(country, address_id) 
VALUES ('Canada', (SELECT address_id FROM a_id)); 

WITH manager_name AS(INSERT INTO person_name(first_name, middle_name, last_name)
 		VALUES ('Ms. Canada', 'Branch', 'Manager') RETURNING name_id),
 manager_id AS(INSERT INTO employee(branch_id, name_id, hire_date, yearly_salary, position_id)
		VALUES ((SELECT branch_id FROM branch WHERE country = 'Canada'), (SELECT name_id FROM manager_name), '2020-01-01', 65000, 1)
		RETURNING employee_id)
UPDATE branch SET branch_manager =(SELECT employee_id FROM manager_id) 
	WHERE branch_id = (SELECT branch_id FROM branch WHERE country = 'Canada');

WITH a_id AS 
(INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country) 
	VALUES ('W3W5W5', 3, 32, 800, 'Branch Blvd.', 'New York', 'NY', 'United States') RETURNING address_id)
INSERT INTO branch(country, address_id) 
VALUES ('United States', (SELECT address_id FROM a_id)); 

WITH manager_name AS(INSERT INTO person_name(first_name, middle_name, last_name)
 		VALUES ('Mr. US', 'Branch', 'Manager') RETURNING name_id),
 manager_id AS(INSERT INTO employee(branch_id, name_id, hire_date, yearly_salary, position_id)
		VALUES ((SELECT branch_id FROM branch WHERE country = 'United States'), (SELECT name_id FROM manager_name), '2018-06-02', 63000, 1)
		RETURNING employee_id)
UPDATE branch SET branch_manager =(SELECT employee_id FROM manager_id) 
	WHERE branch_id = (SELECT branch_id FROM branch WHERE country = 'United States');

--Bookings and Reviews--
INSERT INTO payment(host_id, guest_id, payment_type_id, amount, status)
VALUES (1, 1, 4, 500, 'Approved');

INSERT INTO rental_agreement(property_id, guest_id, host_id, document_link, signed, signing_date, start_date, end_date, payment_id)
VALUES(1, 1, 1,'https://docs.google.com/document/d/1WjeVaITTmj7MrJcuwJwEFEogbVrSPb5332LHaNLaba8/edit?usp=sharing', TRUE, 
	  '2020-01-04', '2020-02-02', '2020-02-04', 1);

INSERT INTO review(guest_id, property_id, overall_rating, communication_rating, clean_rating, value_rating)
VALUES(1, 1, 4, 5, 4, 4);


INSERT INTO payment(host_id, guest_id, payment_type_id, amount, status)
VALUES (1, 1, 3, 270, 'Approved');

INSERT INTO rental_agreement(property_id, guest_id, host_id, document_link, signed, signing_date, start_date, end_date, payment_id)
VALUES(2, 1, 1,'https://docs.google.com/document/d/1WjeVaITTmj7MrJcuwJwEFEogbVrSPb5332LHaNLaba8/edit?usp=sharing', TRUE, 
	  '2020-03-01', '2020-03-10', '2020-03-11', 2);

INSERT INTO review(guest_id, property_id, overall_rating, communication_rating, clean_rating, value_rating)
VALUES(1, 2, 3, 3, 5, 4);


INSERT INTO payment(host_id, guest_id, payment_type_id, amount, status)
VALUES (2, 1, 2, 456, 'Approved');

INSERT INTO rental_agreement(property_id, guest_id, host_id, document_link, signed, signing_date, start_date, end_date, payment_id)
VALUES(3, 1, 2,'https://docs.google.com/document/d/1WjeVaITTmj7MrJcuwJwEFEogbVrSPb5332LHaNLaba8/edit?usp=sharing', TRUE, 
	  '2020-02-21', '2020-02-23', '2020-02-25', 3);

INSERT INTO review(guest_id, property_id, overall_rating, communication_rating, clean_rating, value_rating)
VALUES(1, 3, 5, 5, 4, 5);


INSERT INTO payment(host_id, guest_id, payment_type_id, amount, status)
VALUES (1, 2, 4, 372, 'Approved');

INSERT INTO rental_agreement(property_id, guest_id, host_id, document_link, signed, signing_date, start_date, end_date, payment_id)
VALUES(1, 2, 1,'https://docs.google.com/document/d/1WjeVaITTmj7MrJcuwJwEFEogbVrSPb5332LHaNLaba8/edit?usp=sharing', TRUE, 
	  '2020-02-25', '2020-02-26', '2020-02-28', 4);

INSERT INTO review(guest_id, property_id, overall_rating, communication_rating, clean_rating, value_rating)
VALUES(2, 1, 4, 3, 4, 5);


INSERT INTO payment(host_id, guest_id, payment_type_id, amount, status)
VALUES (2, 2, 4, 467, 'Approved');

INSERT INTO rental_agreement(property_id, guest_id, host_id, document_link, signed, signing_date, start_date, end_date, payment_id)
VALUES(3, 2, 2,'https://docs.google.com/document/d/1WjeVaITTmj7MrJcuwJwEFEogbVrSPb5332LHaNLaba8/edit?usp=sharing', TRUE, 
	  '2020-03-30', '2020-04-02', '2020-04-06', 5);

INSERT INTO review(guest_id, property_id, overall_rating, communication_rating, clean_rating, value_rating)
VALUES(2, 4, 3, 3, 3, 2);



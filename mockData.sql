-- Information for Host 1 --
INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('A1A1A1', 1, 12, NULL, 'Street St.', 'Ottawa', 'ON', 'Canada');

INSERT INTO person_name(first_name, middle_name, last_name) 
VALUES ('Ellie', NULL, 'Rumsey');

INSERT INTO host(address_id, name_id, email, phone_number, active) 
VALUES (1, 1, 'email@gmail.com', '1111111111', 'Y');

-- Information for Properties of Host 1 --
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

SELECT * FROM PROPERTY;

-- Guests --
INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country)
VALUES ('G0G0G0', 1, 5, 14, 'Red Rd.', 'Moncton', 'NB', 'Canada');

INSERT INTO person_name(first_name, middle_name, last_name) 
VALUES ('Charlie', 'Alpha', 'Omega');

INSERT INTO guest(address_id, name_id, email, phone_number)
VALUES(4, 2,'charlieo@gmail.com', '6133456549');



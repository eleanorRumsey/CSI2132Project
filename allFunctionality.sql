--SQL queries as used throughout application. Words in <brackets> represent user input.--

--LOGIN--
SELECT guest_id FROM guest WHERE email='<email>';
SELECT host_id FROM host WHERE email='<email>';

--SIGN UP--
SELECT address_type_id FROM address_type WHERE address_type = 'Personal residence';

INSERT INTO address(postal_code, address_type_id, street_number, unit, street_name, city, province, country) 
	VALUES (<postal_code>, <address_type_id>, <street_number>, <unit>, <street_name>, <city>, <province>, <country>)
RETURNING address_id;

INSERT INTO person_name (first_name, middle_name, last_name) VALUES(<firstName>, <middleName>, <lastName>) RETURNING name_id;

INSERT INTO guest (address_id, name_id, email, phone_number) VALUES (<address_id>, <name_id>, <email>, <phone>);
INSERT INTO host (address_id, name_id, email, phone_number, active) VALUES (<address_id>, <name_id>, <email>, <phone>, 'Y');

--VIEW ALL AVAILABLE PROPERTIES--
SELECT p.property_id, p.property_name, p.property_type_id, p.room_type_id, p.address_id, p.guest_capacity, p.num_bathrooms, 
	p.num_bedrooms, p.next_available_date, p.description, p.rate, p.active, p.image, pt.property_type, rt.room_type, 
	ad.postal_code, ad.street_number, ad.unit, ad.street_name, ad.city, ad.province, ad.country
FROM property p
	JOIN room_type rt ON p.room_type_id = rt.room_type_id
	JOIN property_type pt ON pt.property_type_id = p.property_type_id
	JOIN address ad ON ad.address_id = p.address_id
	JOIN address_type adt ON adt.address_type_id = ad.address_type_id
WHERE adt.address_type = 'Rental property';

--IN ANY PROPERTY SEARCH, VIEW BED SETUP--
SELECT bed_type, num_of_beds FROM bed_setup WHERE property_id = <property_id>;

--IN ANY PROPERTY SEARCH, VIEW PROPERTY RULES--
SELECT rule_type FROM rules WHERE rule_id IN (SELECT rule_id FROM property_rules WHERE property_id = <property_id>);

--IN ANY PROPERTY SEARCH, VIEW PROPERTY AMENITIES--
SELECT amenity_type FROM amenity WHERE amenity_id IN (SELECT amenity_id FROM property_amenities WHERE property_id = <property_id>);

--SEARCH PROPERTIES BY CITY--
SELECT p.property_id, p.property_name, p.address_id, p.guest_capacity, p.num_bathrooms, p.num_bedrooms, p.next_available_date, 
	p.description, p.rate, p.active, p.image, pt.property_type, rt.room_type, ad.postal_code, ad.street_number, ad.unit, 
	ad.street_name, ad.city, ad.province, ad.country
FROM property p
	JOIN room_type rt ON p.room_type_id = rt.room_type_id
	JOIN property_type pt ON pt.property_type_id = p.property_type_id
	JOIN address ad ON ad.address_id = p.address_id
	JOIN address_type adt ON adt.address_type_id = ad.address_type_id
WHERE adt.address_type = 'Rental property' AND ad.city LIKE '%<city>%'"

--SEARCH PROPERTIES BY DATE--
SELECT p.property_id, p.property_name, p.address_id, p.guest_capacity, p.num_bathrooms, p.num_bedrooms, p.next_available_date, 
	p.description, p.rate, p.active, p.image, pt.property_type, rt.room_type, ad.postal_code, ad.street_number, ad.unit, 
	ad.street_name, ad.city, ad.province, ad.country
FROM property p
	JOIN room_type rt ON p.room_type_id = rt.room_type_id
	JOIN property_type pt ON pt.property_type_id = p.property_type_id
	JOIN address ad ON ad.address_id = p.address_id
	JOIN address_type adt ON adt.address_type_id = ad.address_type_id
WHERE adt.address_type = 'Rental property' AND p.next_available_date < <date>;

--VIEW CURRENT AND UPCOMING BOOKINGS AS A GUEST--
SELECT p.property_id, p.property_name, ra.guest_id, ra.start_date, ra.end_date, pt.property_type, rt.room_type, p.rate, 
	p.num_bedrooms, p.num_bathrooms, p.description, p.image, pm.amount, pmt.payment_type, pm.status, ad.postal_code, 
	ad.street_number, ad.unit, ad.street_name, ad.city, ad.province, ad.country
FROM rental_agreement ra 
	JOIN property p ON ra.property_id = p.property_id
	JOIN property_type pt ON pt.property_type_id = p.property_type_id
	JOIN room_type rt ON rt.room_type_id = p.room_type_id
	JOIN payment pm on pm.payment_id = ra.payment_id
	JOIN payment_type pmt on pmt.payment_type_id = pm.payment_type_id
	JOIN address ad ON ad.address_id = p.address_id
WHERE ra.guest_id = <guest_id> AND end_date >= NOW();

--VIEW PAST BOOKINGS AS GUEST--
SELECT p.property_id, p.address_id, p.property_name, ra.guest_id, ra.start_date, ra.end_date, pt.property_type, rt.room_type, 
	p.rate, p.num_bedrooms, p.num_bathrooms, p.description, p.image, pm.amount, pmt.payment_type, pm.status
FROM rental_agreement ra 
	JOIN property p ON ra.property_id = p.property_id
	JOIN property_type pt ON pt.property_type_id = p.property_type_id
	JOIN room_type rt ON rt.room_type_id = p.room_type_id
	JOIN payment pm on pm.payment_id = ra.payment_id
	JOIN payment_type pmt on pmt.payment_type_id = pm.payment_type_id
WHERE ra.guest_id = $guest_id AND end_date < NOW();

--CREATE A NEW BOOKING AS GUEST--
--- initially, create a 'Pending' payment record of $0 cash ---
INSERT INTO payment(host_id, guest_id, payment_type_id, amount, status)
VALUES(<host>, <guest>, 1, 0, 'Pending') RETURNING payment_id;

INSERT INTO rental_agreement (property_id, guest_id, host_id, document_link, signed, signing_date, start_date, end_date, payment_id)
VALUES (<property>, <guest>, <host>, <doc_link>, TRUE, NOW(), <start>, <end>, <payment_id>);

UPDATE property SET next_available_date = <end> WHERE property_id = <property>;

--SUBMIT PAYMENT FOR BOOKING AS GUEST--
UPDATE payment SET payment_type_id = <payment_type>, amount = <total>, status = 'Approved'
WHERE payment_id = <payment_id>;

--VIEW ALL GUEST BOOKING ACTIVITY AS EMPLOYEE--
SELECT p.property_id, p.address_id, p.property_name, p.rate, ra.guest_id, ra.start_date, ra.end_date, pm.amount, pmt.payment_type, 
	pm.status, glv.first_name, glv.last_name, glv.postal_code as guest_postal_code
FROM rental_agreement ra 
	JOIN property p ON ra.property_id = p.property_id
	JOIN payment pm on pm.payment_id = ra.payment_id
	JOIN payment_type pmt on pmt.payment_type_id = pm.payment_type_id
	JOIN guestlistview glv on glv.guest_id = ra.guest_id;






	
<?php
require __DIR__.'/SnoopiClient.php';

$my_api = new SnoopiClient();

print("TEST1: get list of zip codes within 5 miles of origin zip code\n");
$result = $my_api->get_zip_code_radius("11214", "5");
print($result);
print("\n\n");

print("TEST2: get location by ip\n");
$my_ip = "173.56.45.134";
$result = $my_api->get_location_by_ip($my_ip);
print($result);
print("\n\n");

print("TEST3: get distance between two zip codes");
$start_zip = "10314";
$end_zip = "27513";
$result = $my_api->get_zip_code_distance($start_zip, $end_zip);
print($result);
print("\n\n");

print("TEST4: get list of states\n");
$result = $my_api->get_states();
print($result);
print("\n\n");

print("TEST5: get state abbreviation\n");
$state = "New York";
$result = $my_api->get_state_abbreviation($state);
print($result);
print("\n\n");

print("TEST6: get a list of cities\n");
$result = $my_api->get_cities();
print($result);
print("\n\n");

print("TEST7: get a list of cities in New York\n");
$state_abbreviation = "NY";
$result = $my_api->get_cities($state_abbreviation);
print($result);
print("\n\n");


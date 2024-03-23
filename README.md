# Freetimers Candidate Test - Top Soil Bag Calculator

The following test is to ensure that you have the technical skills required to perform daily
duties in the job for which you have applied. It is in your best interests to complete this test
in full without plagiarising the work of others.
Although this task is not timed we estimate that it should not take you more than two hours
to complete and submit in full.

Requirements:
Please do not use a framework (e.g. Laravel or Symfony) for this task.
Create a PHP class that will calculate the number of bags of topsoil needed to surface a
back garden.
Your class should have (but not be restricted to) the following methods:
* A method to set the measurement unit (metres, feet, or yards)
* A method to set the depth measurement unit (centimetres or inches)
* A method to set the dimensions (width, length, and depth)
* A method to calculate the number of bags needed to cover the dimensions
* Amethod to save the object into a MySQL Database (MariaDb 10.1)
You should add a PHP Unit test for each method of your object
Your code will be compatible with PHP 7.4
Your code will comply with PSR-0, and PSR-2 coding standards
Build a front-end interface to use the calculator
Work out the cost of for all the rolls
Add an ‘add to basket’ method/class.

Calculations/important information:
◦ Topsoil bag cost: £72 (inc VAT)<br />
◦ Bag quantity calculation:<br />
◦ metres squared * 0.025 = X<br />
◦ X * 1.4 = Y<br />
◦ Round Y up to the nearest 1 = your number of bags<br />
◦ Example: 110 * 0.025 = 2.75 * 1.4 = 3.85 = 4 Bags of Top Soil<br />

### Installation Guide
Run command: git clone https://github.com/SumAnna/freetimers.git<br />
Run command: composer install<br />
Run freetimers.sql script in your database client or use command line.<br />
Copy .env.example file to your .env file and set the correct values for your DB.<br />
To run phpUnit tests run the command: ./vendor/bin/phpunit<br />

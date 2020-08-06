# ParsePDFPeople
Parses a PDF file for an identifier and generates a CSV with people data.

1. Save your PDF in the project directory.
2. `composer install`
3. `cp config.php.example config.php`
  - Change the value of `$file` to be the filename of your PDF.
  - Change the values of `$user` and `$pass` to be your account's credentials.
  - Update the other LDAP connection variables as needed.
  - Change `$pattern` to be a regular expression that returns a unique identifier value for a person's LDAP record in the first matching parens.
  - Change `$match` to be the name of the LDAP attribute matching the value returned by the pattern.
  - Update the list of attribute names in `$attr` to include any information you want returned in the results. This script generates a CSV with these columns.
4. Run the script with `php parse.php > records.csv`

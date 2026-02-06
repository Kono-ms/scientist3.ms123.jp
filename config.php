<?php

// DB
define('DB_HOST', 'mysql203.xbiz.ne.jp');
define('DB_USERNAME', 'ms123_scientist3');
define('DB_PASSWD', 'x7WYr3a9');
define('DB_DBNAME', 'ms123_scientist3');

// Domain
define('BASE_URL', 'https://scientist3.ms123.jp');

// Mail
define('SENDER_EMAIL', 'info@scientist-cube.com');
//define('SENDER_EMAIL', 'info@scientist3.ms123.jp');
//define('SENDER_EMAIL', '197583@gmail.com');
//define('SENDER_EMAIL', 'med.work.f@gmail.com');
//define('SENDER_EMAIL', 'h.tsurumi@ms123.co.jp');

define('SENDER_NAME', 'Scientist3');

// Website Name
define('WEBSITE_NAME', 'Scientist3');

// Company Name
define('COMPANY_NAME', 'コスモ・バイオ株式会社');

// Account Caption
define('M1_CAPTION', 'Supplier');
define('M2_CAPTION', 'Researchers');
define('O1_CAPTION', '募集情報登録');
define('O2_CAPTION', '希望情報登録');


//運営アカウント
define('M1_SYSTEM_MID', 'M199999');
define('M2_SYSTEM_MID', 'M299999');

// Country
define('COUNTRY_LIST', "" . 
  "United States of America::" . 
  "United Kingdom::" . 
  "Sweden::" . 
  "France::" . 
  "Germany::" . 
  "Switzerland::" . 
  "Japan::" . 

  //"----------::" . 

  "Afghanistan::" . 
  "Albania::" . 
  "Algeria::" . 
  "Andorra::" . 
  "Angola::" . 
  "Antigua and Barbuda::" . 
  "Argentina::" . 
  "Armenia::" . 
  "Australia::" . 
  "Austria::" . 
  "Azerbaijan::" . 
  "Bahamas::" . 
  "Bahrain::" . 
  "Bangladesh::" . 
  "Barbados::" . 
  "Belarus::" . 
  "Belgium::" . 
  "Belize::" . 
  "Benin::" . 
  "Bhutan::" . 
  "Bolivia::" . 
  "Bosnia and Herzegovina::" . 
  "Botswana::" . 
  "Brazil::" . 
  "Brunei Darussalam::" . 
  "Bulgaria::" . 
  "Burkina Faso::" . 
  "Burundi::" . 
  "Cabo Verde::" . 
  "Cambodia::" . 
  "Cameroon::" . 
  "Canada::" . 
  "Central African Republic::" . 
  "Chad::" . 
  "Chile::" . 
  "China::" . 
  "Colombia::" . 
  "Comoros::" . 
  "Congo::" . 
  "Cook Islands::" . 
  "Costa Rica::" . 
  "Cote d'Ivoire::" . 
  "Croatia::" . 
  "Cuba::" . 
  "Cyprus::" . 
  "Czechia::" . 
  "Democratic Republic of Congo::" . 
  "Denmark::" . 
  "Djibouti::" . 
  "Dominica::" . 
  "Dominican Republic::" . 
  "Ecuador::" . 
  "Egypt::" . 
  "El Salvador::" . 
  "Equatorial Guinea::" . 
  "Eritrea::" . 
  "Estonia::" . 
  "Eswatini::" . 
  "Ethiopia::" . 
  "Fiji::" . 
  "Finland::" . 
  "Gabon::" . 
  "Gambia::" . 
  "Georgia::" . 
  "Ghana::" . 
  "Greece::" . 
  "Grenada::" . 
  "Grenadines::" . 
  "Guatemala::" . 
  "Guinea::" . 
  "Guinea-Bissau::" . 
  "Guyana::" . 
  "Haiti::" . 
  "Holy See::" . 
  "Honduras::" . 
  "Hong Kong::" . 
  "Hungary::" . 
  "Iceland::" . 
  "India::" . 
  "Indonesia::" . 
  "Iran::" . 
  "Iraq::" . 
  "Ireland::" . 
  "Israel::" . 
  "Italy::" . 
  "Jamaica::" . 
  "Jordan::" . 
  "Kazakhstan::" . 
  "Kenya::" . 
  "Kiribati::" . 
  "Kuwait::" . 
  "Kyrgyzstan::" . 
  "Lao People's Democratic::" . 
  "Latvia::" . 
  "Lebanon::" . 
  "Lesotho::" . 
  "Liberia::" . 
  "Libya::" . 
  "Liechtenstein::" . 
  "Lithuania::" . 
  "Luxembourg::" . 
  "Madagascar::" . 
  "Malawi::" . 
  "Malaysia::" . 
  "Maldives::" . 
  "Mali::" . 
  "Malta::" . 
  "Marshall Islands::" . 
  "Mauritania::" . 
  "Mauritius::" . 
  "Mexico::" . 
  "Micronesia (Federated States::" . 
  "Monaco::" . 
  "Mongolia::" . 
  "Montenegro::" . 
  "Morocco::" . 
  "Mozambique::" . 
  "Myanmar::" . 
  "Namibia::" . 
  "Nauru::" . 
  "Nepal::" . 
  "Netherlands::" . 
  "New Zealand::" . 
  "Nicaragua::" . 
  "Niger::" . 
  "Nigeria::" . 
  "Niue::" . 
  "Norway::" . 
  "Oman::" . 
  "Pakistan::" . 
  "Palau::" . 
  "Panama::" . 
  "Papua New Guinea::" . 
  "Paraguay::" . 
  "Peru::" . 
  "Philippines::" . 
  "Poland::" . 
  "Portugal::" . 
  "Qatar::" . 
  "Republic::" . 
  "Republic of Korea::" . 
  "Republic of Moldova::" . 
  "Republic of North Macedonia::" . 
  "Republic of Serbia::" . 
  "Romania::" . 
  "Russian Federation::" . 
  "Rwanda::" . 
  "Saint Kitts and Nevis::" . 
  "Saint Lucia::" . 
  "Saint Vincent and the::" . 
  "Samoa::" . 
  "San Marino::" . 
  "Sao Tome and Principe::" . 
  "Saudi Arabia::" . 
  "Senegal::" . 
  "Seychelles::" . 
  "Sierra Leone::" . 
  "Singapore::" . 
  "Slovakia::" . 
  "Slovenia::" . 
  "Solomon Islands::" . 
  "Somalia::" . 
  "South Africa::" . 
  "South Sudan::" . 
  "Spain::" . 
  "Sri Lanka::" . 
  "Sudan::" . 
  "Suriname::" . 
  "Syrian Arab Republic::" . 
  "Taiwan::" . 
  "Tajikistan::" . 
  "Thailand::" . 
  "Timor-Leste::" . 
  "Togo::" . 
  "Tonga::" . 
  "Trinidad and Tobago::" . 
  "Tunisia::" . 
  "Turkey::" . 
  "Turkmenistan::" . 
  "Tuvalu::" . 
  "Uganda::" . 
  "Ukraine::" . 
  "United Arab Emirates::" . 
  "United Republic of Tanzania::" . 
  "Uruguay::" . 
  "Uzbekistan::" . 
  "Vanuatu::" . 
  "Venezuela::" . 
  "Viet Nam::" . 
  "Yemen::" . 
  "Zambia::" . 
  "Zimbabwe");

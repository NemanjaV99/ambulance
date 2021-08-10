# Ambulance

## Setup
Once you have cloned the repository, cd to the ```/ambulance``` directory and run the following commands:

1. Install dependencies with composer  
``` composer install ```  

2. Run npm install  
``` npm install ```  

3. Build assets, run npm script  
``` npm run dev ```  

4. Create a copy of the .env file named .env.local  
``` cp .env .env.local ``` 

5. Configure the database URL, inside the .env.local file ( Your settings might be different, but the database name should be **ambulance** )  
``` DATABASE_URL="mysql://root:@127.0.0.1:3306/ambulance?serverVersion=5.7" ```  

6. Create the database  
``` php bin/console doctrine:database:create ```  

7. Create the migration  
``` php bin/console make:migration ```  

8. Run the migration  
``` php bin/console doctrine:migrations:migrate ```

9. Run the db fixtures, insert test db data  
``` php bin/console doctrine:fixtures:load ```

After that you should have a working application, to test it you can use the following test accounts:  

| Account | Username  | Password |
|-------------- | ------------- | ------------- |
| Admin | admin  | admin123 |
| Doctor  | doctor  | doctest123 |
| Doctor 2 | doctor2 | doctor123 | 
| Counter | counter | counter123 |

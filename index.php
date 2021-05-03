<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/img/logo.svg" type="image/x-icon">
    <title>AJ-dev-api</title>
    <link rel="stylesheet" href="assets/css/style.css" type="text/css">
</head>
<body>

<div  class="container">
    <h1>AJ-dev-api</h1>
    <div class="divbody">
        <h3>Auth - login</h3>
        <code>
           POST  /auth 
           <br>
           {
               <br>
               "usuario" :"",  -> REQUERIDO
               <br>
               "password": "" -> REQUERIDO
               <br>
            }
        
        </code>
    </div>      
    <div class="divbody">   
        <h3>Balones</h3>
        <code>
           GET  /balones
           <br>
           GET  /balones/$1
        </code>

        <code>
           POST  /balones
           <br> 
           {
            <br> 
               "serial": "",               -> REQUERIDO
               <br> 
               "capacidad": "",            -> REQUERIDO
               <br> 
               "tulipa":"",                -> REQUERIDO
               <br> 
               "marca":"",                 -> REQUERIDO 
               <br>   
               "estado":"",                -> REQUERIDO
               <br> 
               "operacion":"",                 -> REQUERIDO 
               <br>        
               "token": ""                 -> REQUERIDO        
               <br>       
           }

        </code>
        <code>
           PUT  /balones/$1
           <br> 
           {
            <br>
               "capacidad": "",
               <br> 
               "tulipa":"",
               <br> 
               "marca":"",
               <br> 
               "estado":"",
               <br> 
               "operacion":"", 
               <br>        
               "token": ""                 -> REQUERIDO        
               <br>       
           }

        </code>
        <code>
           DELETE  /balones/$1
           <br> 
           {   
               <br>    
               "token" : "",                -> REQUERIDO        
               <br>
           }

        </code>
    </div>


</div>
    
</body>
</html>


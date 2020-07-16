# Backend Challenge

### Endpoints:

- GET         /categorias                                     Lista todas las categorías
- POST        /solicitudes-presupuesto                        Crea una nueva solicitud de presupuesto
- PUT         /solicitudes-presupuesto/{id}/modificar         Modifica una solicitud de presupuesto
- PUT         /solicitudes-presupuesto/{id}/publicar          Publica una solicitud de presupuesto
- PUT         /solicitudes-presupuesto/{id}/descartar         Descarta una solicitud de presupuesto
- GET         /solicitudes-presupuesto                        Lista todas las solicitudes de presupuesto o sólo para un email de forma paginada

### Payloads:


Creación:

{

    "title": string,
    
    "description": string required,
    
    "category_id": integer,
    
    "user_data": 
    
        {
        
            "email": string required,
            
            "phone": integer required,
            
            "address": string required  
            
        }   
        
}




Modificación:

{

    "title": string,
    
    "description": string,
    
    "category_id": integer
    
}




Listado paginado:

{

    "email": string,
    
    "limit": integer,
    
    "offset": integer
    
}

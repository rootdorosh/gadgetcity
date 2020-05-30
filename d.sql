SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

UPDATE auto_model SET image = (
    SELECT auto_model_year.image 
    FROM auto_model_year 
    WHERE auto_model.id = auto_model_year.model_id AND auto_model_year.year IS NOT NULL  
    ORDER BY year DESC 
    LIMIT 1
);
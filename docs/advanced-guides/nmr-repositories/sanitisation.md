# Data Sanitisation and Missing Values

Due to the data being reported, in most cases, as free text; in addition to the databases having their own schemas for data modeling (with few exceptions, as [MetaboLights](https://www.ebi.ac.uk/metabolights/) complies to [ISA Model](https://isa-specs.readthedocs.io/en/latest/isamodel.html)), we had to generate “personalized” scripts for each database to handle its specific way of data reporting; with data being exposed via standard API or not; within a dedicated field or not; as an ontology term or a free text; with a unit in the same field, or in another one, or completely absent. That also led to a lot of cleaning through hard coding, and also to considering some data as missing as the unit is not provided, making the data vague even for human reading, while sometimes cleaning even with hard coding wasn’t possible as each value will need its own cleaning (e.g., with the solvents, unless mapped to an ontology which is out of the scope of this analysis).

We provide a detailed description of the data sanitization we performed based on the repository and the parameter in the dedicated pages of the parameters.
- We describe the **Field Type** from which the parameter can be extracted as either:
  - **dedicated** when the field purpose is to provide this parameter.
  - **semi-dedicated** when it is not mainly concerned with providing the desired parameter, but still, it can be found there in most cases. 
  - **absent** means that no such field was found in the database. 
- Then, the field name is mentioned. 
- Afterwards, given that there is a dedicated or semi-dedicated field, we describe the **Values Readability** whether they are:
  - **ontology-driven**.
  - **machine-readable** when they are free text, but reasonable cleaning makes them machine-readable in most cases.
  - **free text**
- We describe whether the **Unit** was provided as a 
  - **separate field** 
  - **integrated** with the values (as this option usually results from free text input, it leads to the possibility of not providing the unit in some cases). 
  - If no unit is needed, we write it in the table as **none**.  
- We also provide examples of the **Input** (the values the parameter got in the repository) with the **Output** we generated from them, with clarification of the meaning of the value **missing** as an output.

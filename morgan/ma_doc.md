

? questions/réflexions/recherches


## CODE 

### Retours problèmes

#### Erreurs

##### Bloquantes

##### Non bloquantes

*  import acte : 2021-02-04 13:40:04 [ERROR] ba-tp@chatnoir.lautre.net > Aucun nouvel id trouvé pour l'insert dans (pas d'info derrière) (* plusieurs fois pour colonne différentes)    
(file:///home/morgan/internet/buenosaires/morgan/Capture-error_log-import-acte-210204.png)[Capture-error_log-import-acte-210204.png]    

*   export actes : 2021-02-04 13:40:04 [ERROR] ba-tp@chatnoir.lautre.net > SQL error : Incorrect integer value: '' for column 'nom_id' at row 1 (* plusieurs fois pour colonne différentes)       
(file:///home/morgan/internet/buenosaires/morgan/Capture-error_log-import2-acte-210204.png)[Capture-error_log-import2-acte-210204.png]    


#### Warnings

*  Warning: Declaration of Database::query($requete) should be compatible with mysqli::query($query, $resultmode = NULL) in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 138   

*  Warning: count(): Parameter must be an array or an object that implements Countable in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 42    

*  Pareil dans Database.php on line 44    

*  Warning: Use of undefined constant SQL_SERVER - assumed 'SQL_SERVER' (this will throw an Error in a future version of PHP) in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 15
Warning: Use of undefined constant SQL_USER - assumed 'SQL_USER' (this will throw an Error in a future version of PHP) in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 16
Warning: Use of undefined constant SQL_PASS - assumed 'SQL_PASS' (this will throw an Error in a future version of PHP) in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 17
Warning: Use of undefined constant SQL_DATABASE_NAME - assumed 'SQL_DATABASE_NAME' (this will throw an Error in a future version of PHP) in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 18



#### Notices

*  Notice: Undefined property: Prenom::$table_name in /home/morgan/internet/buenosaires/src/class/io/Database.php on line 571
et pour chaque colonne, en important un fichier .xml
L'import a l'air de se faire correctement.

#### Deprecateds

*  Deprecated: Methods with the same name as their class will not be constructors in a future version of PHP; Account has a deprecated constructor in /home/morgan/internet/buenosaires/src/class/model/Account.php on line 5
+ d'autres classes qui affichent la même info    



#### Autres

*  Export fichier "toutes les relations" ne comporte pas de données. Dans ma BDD j'ai l'acte de mariage de Belgrano et celui d'une de ses filles. Il ne devrait pas y avoir au moins une relation entre eux 2 ?

*   Afin de maintenir la rétrocompatibilité avec PHP 4, PHP 5 continue d'accepter **l'usage du mot-clé var pour la déclaration de propriétés** en remplacement de (ou en plus de) public, protected, et private. Cependant, var n'est plus requis par le modèle objet de PHP 5. Pour les versions allant de PHP 5.0 à 5.1.3, l'usage de var était considéré comme obsolète et déclenchait un avertissement de niveau E_STRICT, mais depuis PHP 5.1.3, l'usage n'est plus obsolète et ne déclenche plus d'avertissement.
Si vous déclarez une propriété en utilisant var au lieu de public, protected, ou private, alors PHP 5 traitera la propriété comme si elle avait été déclarée comme public. 
=> remplacer _var_ par _public_ sera plus clair.

*  var = [] à remplacer par var = array() ? (ex Acte.php ln 23)


### Structure / organisation

**config.php** regroupe des **variables et CONSTANTES**
**utils.php** regroupe des **fonctions**

Dans les **classes** (sauf Database), un certain nombre de **méthodes** sur la bdd sont **communes** : les **regrouper**.







## TECHNOS V2.1


**Bibliothèques**
    



## TECHNOS V2.0


**Bibliothèques**
    



## RECHERCHES

**Bibliothèques**

*  ? Lodash/underscore VS ES6 ?
doc lodash : (https://lodash.com/docs/)[https://lodash.com/docs/]
Comparatif lodash / ES6 : (https://blog.arca-computing.fr/lodash-underscore-vs-es6/)[https://blog.arca-computing.fr/lodash-underscore-vs-es6/]

*  ? Bower ? gestionnaire de paquets js
doc : (https://bower.io/)[https://bower.io/]
"Bower requires node, npm and git." ==> ça fait un peu beaucoup de gestionnaires, **voir si un gestionnaire peut englober la gestion des paquets et dépendances en même temps (style composer pour php)**.
"...psst! While Bower is maintained, we recommend using (https://yarnpkg.com/)[Yarn] and (https://webpack.js.org/)[Webpack] or (https://parceljs.org/)[Parcel] for front-end projects (https://bower.io/blog/2017/how-to-migrate-away-from-bower/)[read how to migrate!]"
Impose beaucoup de balises script et link en html

    
*  ? voir si possible de faire genre de helpers ?    



# EXTRA

? Redux ?


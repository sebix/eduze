#Controleurs

Dans une application MVC, les Controleurs se placent entre les Mod�les et les Vues. Ils passent l'information aux mod�les lorsque les donn�es n�cessitent des traitements et ils demandent l'information n�cessaire aux mod�les. Les Controleurs transmettent les informations du Mod�le aux Vues qui ciontiennent le code � afficher aux utilisateurs.

Les controleurs sont appel�s par rapport � l'URL appel�e, pour plus d'informations consulter la documentation sur [les URLs et les Liens](about.urls).

## Nommage des controleurs et fonctionnement

Le nom d'un controleur doit correspondre exactement au nom de fichier.

**Conventions d'�criture**

* les nom de fichiers des controleurs doivent �tre en minuscule, e.g. `articles.php`
* ils doivent �tre situ�s dans le (sous-)dossier **classes/controller**, e.g. `classes/controller/articles.php`
* la classe du controleur doit correspondre au fichier, commencer par une majuscule et �tre pr�fix�e par **Controller_**, e.g. `Controller_Articles`
* elle doit h�rit� de la classe Controller
* les m�thodes du controleur doivent �tre pr�c�d�es de **action_** (e.g. `action_do_something()` ) pour pouvoir �tre appel�es par rapport � l'URL



### Un controleur simple

Ci-dessous un exemple de controleur qui affiche Hello World � l'�cran.

**application/classes/controller/article.php**
~~~
<?php defined('SYSPATH') OR die('No direct access allowed.');
 
class Controller_Article extends Controller
{
    public function action_index()
    {
        echo 'Hello World!';
    }
}
~~~
Si vous entrez alors l'URL yoursite.com/article dans votre navigateur (ou yoursite.com/index.php/article sans URL rewritting) vous devriez voir appara�tre:
~~~
Hello World
~~~
C'est tout pour votre premier controleur. Toutes les conventions ont �t� appliqu�es.



### Un controleur plus avanc�

Dans l'exemple ci-dessus la m�thode `index()` est appel�e par l'URL yoursite.com/article. Si le second segment de l'URL est vide, la m�thode index est appel�e par d�faut. Elle pourrait aussi �tre appel�e en entrant l'URL yoursite.com/article/index.

_Si le second segment de l'URL n'est pas vide, il d�termine la m�thode dub controleur � appeler._

**application/classes/controller/article.php**
~~~
class Controller_Article extends Controller
{
    public function action_index()
    {
        echo 'Hello World!';
    }
 
    public function action_overview()
    {
        echo 'Article list goes here!';
    }
}
~~~
Maintenant, si vous entrez l'URL yoursite.com/article/overview vous devriez voir appara�tre:
~~~
Article list goes here!
~~~


### Un controleur avec des arguments

Imaginons que l'on souhaite afficher un article particulier, identifi� par l'id `1` et le titre `your-article-title`.

L'URL ressemblerait alors � yoursite.com/article/view/**your-article-title/1**. Les 2 derniers segments sont pass�es � la m�thode view() du controleur.

**application/classes/controller/article.php**
~~~
class Controller_Article extends Controller
{
    public function action_index()
    {
        echo 'Hello World!';
    }
 
    public function action_overview()
    {
        echo 'Article list goes here!';
    }
 
    public function action_view($title, $id)
    {
        echo $id . ' - ' . $title;
        // you'd retrieve the article from the database here normally
    }
}
~~~
Si vous appelez yoursite.com/article/view/**your-article-title/1** vous devriez voir appara�tre:
~~~
1 - your-article-title
~~~

/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Dossier r�serv� aux modules du bundles back

Explication:
Un module est defini en plussieurs elements.
Avant de commencer nous allons pr�senter la structure du dossier afin de comprendre au mieux un module

======= les dossiers relatives � un modules sont:
code/
configs/
locale/
template/

Pour information le dossier etc/ est r�serv� � la configuration du bundles et non au module.

=======  code/
Le dossier code est r�serv� � l'ensemble du code m�tier et partie logique d'un module
Chaque module comporte son dossier strictement dedi�.

Un module poss�de plusieurs sous dossiers dans le dossier code qui sont:
Example:
moduleName
    controllers/
    forms/
        bluider/
    models/
        collections/
        dbtable/
        shema/
    events/
    presenters/

Le dossier controllers r�presente le dossier li� au �venement d'une r�qu�te.
Forms pour les formulaires.
Models relative au data d'une application par example dbb
Events li�r aux events d�clench�s.
Presenters est li� au methode excut� dans les templates(views).

======= configs
Dossier li� � la configuration des modules
Exemple
configs/
    moduleName/
        event.yml
        deny_rows.yml
        info.yml
        view.yml

=======  locale
Dossier r�serv� � la traduction et � la aide d'un module
Exemple
locale/
    moduleName/
        help/
            help.inc
        translate/
            fr_FR.csv
            en_US.csv

Pour information le fichier help doit �tre obligatoirement en .inc
les traductions sont en csv pour plus de lisibiliter.


=======  template
Dossier r�serv� � la vue et l'affichage d'un module.
Exemple
template/
    layouts/
        layout.php
    scripts/
        moduleName/
            namecontroller/
                nameaction.phtml


===============  pour reconstituer le module dashbord
code/
    moduleName
        controllers/
            forms/
            bluider/
        models/
            collections/
            dbtable/
        events/
        presenters/

configs/
    moduleName/
        event.yml
        info.yml

locale/
    moduleName/
        help/
            help.inc
        translate/
            fr_FR.csv
            en_US.csv
template/
    layouts/
        layout.php
    scripts/
        moduleName/
            namecontroller/
                nameaction.phtml

Un module est donc d�cop�s en plusieurs dossiers pour distinguer les differentes couches d'application
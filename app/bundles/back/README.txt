/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Dossier réservé aux modules du bundles back

Explication:
Un module est defini en plussieurs elements.
Avant de commencer nous allons présenter la structure du dossier afin de comprendre au mieux un module

======= les dossiers relatives à un modules sont:
code/
configs/
locale/
template/

Pour information le dossier etc/ est réservé à la configuration du bundles et non au module.

=======  code/
Le dossier code est réservé à l'ensemble du code métier et partie logique d'un module
Chaque module comporte son dossier strictement dedié.

Un module posséde plusieurs sous dossiers dans le dossier code qui sont:
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

Le dossier controllers répresente le dossier lié au évenement d'une réquête.
Forms pour les formulaires.
Models relative au data d'une application par example dbb
Events liér aux events déclenchés.
Presenters est lié au methode excuté dans les templates(views).

======= configs
Dossier lié à la configuration des modules
Exemple
configs/
    moduleName/
        event.yml
        deny_rows.yml
        info.yml
        view.yml

=======  locale
Dossier réservé à la traduction et à la aide d'un module
Exemple
locale/
    moduleName/
        help/
            help.inc
        translate/
            fr_FR.csv
            en_US.csv

Pour information le fichier help doit être obligatoirement en .inc
les traductions sont en csv pour plus de lisibiliter.


=======  template
Dossier réservé à la vue et l'affichage d'un module.
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

Un module est donc décopés en plusieurs dossiers pour distinguer les differentes couches d'application
/*
 * This file is part of the Factory package.
 *
 * (c) Gerard Toko <gerardtoko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Dossier réservé à la configuration de l'application

Pour information aucun code php ne dois être présent dans ce dossier, seule les fichiers 
de configurations sont permissent par respect de convention

Ficher Important
===== app:
Réserver à la configration relative au controller frontal et au configuration global de l'application
ce ficher est obligatoire ainsi que le fichier shema.yml

on peut retrouvé l'ensembles des plugins définis dans ce fichier
Exemple de fichier app.yml et configuration par default
#Base url
base: /

#namespaces :

#admin :
admin_name: manager
projet_name: Cms Test
default_module: page

#bundles
bundles:
    all: [back, front]
    default:
        bundle: front
        module: [dashbord, default]
        controller: index
        action: index

# prod dev test
env: dev

# plugins
plugins:
    core/db: true
    core/session: true
    core/cache: true
    core/locale: true
    core/dispatch: true
    core/template: true
    core/context: true
    locale/auth: true
    locale/acl: true

#cache
cache:
    type: file
    lifetime: 3600
    active: false
    config: null

#local
locale_default: fr_FR

#auth
auth:
    table_name: users
    identity_column: user_name
    credential_column: user_pass
    crendtial_treatment: SHA1(?)
    
    
error:
    layout404: layout


===== database.yml
ficher de confiration de la base de donnée
ce fichier est nom obligatoire
Exemple
## YAML Template.
default:
    adapter: Pdo_Mysql
    params:
        hostname: localhost
        dbname: cms
        username: root
        password: root

===== session.yml
relative à la configuration d'une session
fichier non obligatoire.
Exemple
#sessions
sessions:
    #option session
    options:
        #absolue directory
        save_path: /Applications/MAMP/htdocs/package/var/session
        use_cookies: on
        use_only_cookies: on
        use_trans_sid: off
        strict: on
        remember_me_seconds: 3600
        name: user
        gc_divisor: 1000
        gc_maxlifetime: 86400
        gc_probability: 1


===== routes.yml
Ce fichier est destiné à definir des routes personnalisées
Permet la récriture des routes
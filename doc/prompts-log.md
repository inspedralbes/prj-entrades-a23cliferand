# IA x Desenvolupar


## Explicació de la funcionalitat

Volia un panell d'administració amb uns gràfics fets amb vue-chartjs preparats perquè a un futur col·locar els panells dels cruds.

## Decisions preses (canvis en prompts o spec)
### Prompts utilitzats per generar l'especificació
/opsx-propose M'agradaria fer la pàgina principal de admin del meu projecte. M'agradaria un side bar per accedir als cruds de les dades. En el resto de la pantalla hi haurà vue-chartjs amb diferent gràfics bàsics d'informació. Utilitzo un css en natiu.

### Prompts utilitzats durant la implementació
/opsx:apply

### Prompts de correcció / refinament
/opsx-explore M'agradaria que mirés la db per a saber com fer els charts

### Errors detectats
L’ia al principi li va costar molt entendre les dades que treballava. El Task que va fer a la primera va ser molt senzill i no va agafar les dades de la db. 

### Com s’han corregit
Gràcies a metode ”explore”, l’ia em va començar a fer preguntes del sistema i de com podíem fer el sistema. Al principi em pensava que em faria un task/spect a part, però el sistema em va donar l’opció d’adaptar l’anterior “propose” amb la nova informació que tenia.

### Relació entre problema i canvi en el prompt
No vaig que haver de fer un prompt molt complicat, openspec s’adapta amb la informació que té. Si fa falta més dades/informació, la demana al programador.

### Valoració crítica real (no superficial)

#### L’agent ha seguit realment l’especificació?
Si

#### Quantes iteracions han estat necessàries?

Dues (un propose i un explore)


#### On falla més la IA (interpretació, execució, coherència)?

La interpretació (OpenCode “peca” sempre en això) i una miqueta amb l’execució.

#### Has hagut de modificar l'especificació o només els prompts?

He tingut per fer un “explore”, d’aquesta manera l’ia ha reinterpretat la solució que tenia amb molta més coherència.

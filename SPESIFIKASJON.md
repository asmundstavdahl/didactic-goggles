Innledning og formål
Prosjektbeskrivelse
ChatPHP er et minimalistisk system for å ha ens egen "ChatGPT", med noen funksjoner som skaperen savner hos andre liknende systemer. Brukere kan velge hvilke modeller og tilbydere den vil bruke, og kun betale API-priser i stedet for å betale et fast månedlig beløp for å få tilgang til en slik tjeneste.
Målgruppe
ChatPHP et tiltenkt brukt av brukere med høy teknisk kompetanse.
Bakgrunn
Skaperen finner ikke et "ChatGPT"-liknende system som kan kjøres på privat server og har tilstrekkelig lav teknisk kompleksitet og samtidig den fleksibiliteten og de funksjonene som han ønsker. ChatPHP vil også by på et grunnleggende utgangspunkt for videreutvikling for de behov som vil oppstå i fremtiden, enten det skulle være agenter, backend for en privat stemmetjener, integrasjon med Home Assistant, e.l.
Omfang
Det legges i første omgang opp til at en installasjon av ChatPHP er ment for én person, uten innebygd innlogging.
Brukergrensesnittet skal være primitivt, men funksjonelt.
Strømming av tokens i responsene skal ikke støttes, av hensyn til teknisk kompleksitet.
Kravspesifikasjon
Funksjonelle krav

Bruker skal kunne ha samtaler med KI.
Flere separate samtaler skal kunne foregå samtidig.
Hver samtale skal ha en samtalehistorikk som inneholder alle meldingene som har blitt sendt fra brukeren, fra KI-en og evt. fra andre kilder som "funksjoner"/"tool use".
Hver melding i samtalehistorikken skal kunne redigeres av brukeren, til og med for meldinger som ikke er fra brukeren og meldinger som er midt i samtalen.

Ikke-funksjonelle krav

JavaScript skal unngås som pesten. Alt skal skje på serversiden. Kun minimale funksjonelle ting skal gjøres med JS, men kun hvis det ikke er mulig å gjøre det med HTML og/eller CSS. Endre heller på HTML-strukturen som sendes fra serveren enn å benytte JavaScript.
Systemet skal være svært enkelt å sette opp. PHP  og php-sqlite bør være nok.
PHP's curl extension skal benyttes for å kommunisere med OpenAI sitt API, ikke deres offisielle SDK.
Færrest mulig tredjeparts komponenter skal benyttes.
Teknisk kompleksitet skal begrenses til et minimum.
All PHP-kode skal ha fullt spesifiserte typer. I alle tilfeller der det ikke er mulig å spesifisere typen nøyaktig med PHPs innebygde typesystem, skal PHPDoc med PHPStan-kompatible typespesifikssjoner benyttes.
Frontend skal være "dum" – bruk standard HTML- og HTTP-oppførsler slik de er ment for å bli brukt.
Data skal lagres i en sqlite-database.
Koden skal lagres i et git-repo.
Et verktøy for kodeformatering, som støtter nyeste offisielle de-facto standard for formatering av PHP-kode, skal benyttes og kjøres før hver gir commit som en pre-commit-hook.

Systemarkitektur
Teknologivalg

Programmeringsspråk : PHP
Rammeverk, backend: ingen
Rammeverk, frontend: ingen
Database: sqlite

Infrastruktur
Programvarer skal kjøres på en hvilken som helst datamaskin som har PHP og php-sqlite – lokalt på en datamaskin, på en privat server eller på en VPS.
Integrasjoner
CharPHP bruker standard "completions"-API, hos en tilbyder som brukeren legger inn i konfigurasjonsfilen, for å få respons fra den store språkmodellen som brukeren legger inn i konfigurasjonsfilen.
Datamodel
conversation

id: Unik identifikator for samtalen (primærnøkkel)
title: Tittel på samtalen
created_at: Tidspunkt for når samtalen ble opprettet
updated_at: Tidspunkt for siste endring i samtalen
model_config: Referanse til en modellkonfigurasjon definert i konfigurasjonsfilen (streng)
system_prompt: Standard systemprompt for samtalen (valgfritt)

message

id: Unik identifikator for meldingen (primærnøkkel)
conversation_id: Referanse til samtalen meldingen tilhører (fremmednøkkel)
sequence: Numerisk verdi som angir meldingsrekkefølgen i samtalen
type: Type avsender ("user", "assistant", "system" or "function")
content: Meldingsteksten
edited_at: Tidspunkt for når meldingen sist ble redigert
created_at: Tidspunkt for når meldingen ble opprettet en

4. Brukergrensesnitt
Designprinsipper

Brukergrensesnittet skal være minimalistisk og funksjonelt.
Grensesnittet skal være selvforklarende og ikke kreve opplæring.
Fokus skal være på innhold fremfor pynt.
Alle funksjoner skal være tilgjengelige uten bruk av JavaScript.
Designet skal være tilpasset personer med høy teknisk kompetanse.

Strukturell oppbygning
Systemet skal ikke ha wireframes eller mockups. Et enkelt HTML-dokument med CSS skal være tilstrekkelig for å beskrive grensesnittet.
Navigasjonsflyt
Navigasjon skal skje gjennom enkle HTML-lenker og forms. Maksimalt to klikk for å nå enhver funksjon i systemet.
Responsivt design
Grensesnittet skal fungere på alle skjermstørrelser uten spesifikk kode for responsivitet. Enkel flytende layout med prosentbaserte størrelser skal benyttes.
5. Datahåndtering
Datamodeller
Datamodellen er allerede definert i systemarkitektur-delen med tabellene conversation og message.
Databasedesign
Databaseskjemaet skal holdes så enkelt som mulig med kun to tabeller. Komplekse relasjoner skal unngås.
Databaseoppsett
Et enkelt SQL-skript for opprettelse av databasen skal være tilstrekkelig.
Datavalidering
Input-validering skal skje med innebygde PHP-funksjoner og uten komplekse validerings-biblioteker.
Personvernhensyn

Systemet er designet for personlig bruk og har ikke innebygd innlogging.
Alle data lagres lokalt på brukerens server.
API-nøkler til eksterne tjenester skal lagres i konfigurasjonsfiler, ikke i databasen.
Det skal være mulig å enkelt eksportere og slette alle data.

6. Testing og kvalitetssikring
Testmetoder
Testing skal primært være manuell. Automatiserte tester skal begrenses til kritiske komponenter.
Testkriterier

Alle CRUD-operasjoner for samtaler og meldinger skal fungere som forventet.
Kommunikasjon med API-er skal håndtere både vellykkede responser og feilsituasjoner.
Brukergrensesnittet skal fungere i alle moderne nettlesere.

Testmiljø
Testmiljøet skal være identisk med produksjonsmiljøet - en enkel PHP-installasjon med SQLite.
Kvalitetsmål

All kode skal følge PSR-12 kodestandarder.
Koden skal ha fullstendig typehinting.
Kritisk funksjonalitet skal ha minst 80% testdekning.

7. Implementasjonsplan
Milepæler

Datamodell og grunnfunksjonalitet for samtaler
API-integrasjon og meldingshåndtering
Redigering av historikk og parallelle samtaler

Utviklingsmetode
Utviklingen skal skje inkrementelt med fokus på én funksjon av gangen som fullføres før neste påbegynnes.
Leveranser

Første leveranse: Fungerende prototype med grunnleggende samtalesystem
Andre leveranse: Fullstendig system med alle kjernefunksjoner

Ressursbehov

Én utvikler med kompetanse innen PHP og SQL
Tilgang til API-nøkler for valgte KI-tjenester
En server eller lokal maskin med PHP og SQLite

8. Drifts- og vedlikeholdsplan
Driftsrutiner
Systemet skal kreve minimal vedlikehold. Ingen automatiske oppdateringer eller komplekse vedlikeholdsprosedyrer.
Support
Siden systemet er for personlig bruk, er formelle supportprosesser unødvendige.
Oppdateringsstrategi

Oppdateringer vil publiseres til git-repositoriet.
Brukeren er selv ansvarlig for å holde systemet oppdatert.
Enkle migreringsskript vil følge med alle oppdateringer som endrer datamodellen.

Dokumentasjon
Dokumentasjon skal være enkel og direkte i koden. Ingen separate dokumentasjonssystemer eller komplekse wiki-strukturer.
9. Risikovurdering
Potensielle risikofaktorer

Endringer i API-er fra KI-tjenesteleverandører
Begrensninger i responstid fra eksterne API-er
Sikkerhetsproblemer ved håndtering av brukerinput

Sannsynlighet og konsekvens

Endringer i API-er: Middels sannsynlighet, høy konsekvens
Begrensninger i responstid: Høy sannsynlighet, lav konsekvens
Sikkerhetsproblemer: Lav sannsynlighet, middels konsekvens

Risikoreduserende tiltak

Modulær kode som enkelt kan tilpasses API-endringer
Enkel håndtering av langvarige API-kall gjennom asynkron lasting av svar
Grunnleggende validering av all brukerinput med innebygde PHP-funksjoner

10. Godkjenningskriterier
Akseptansetester

Systemet kan installeres på en ren PHP-server med minimal konfigurasjon
Brukeren kan opprette, lese, oppdatere og slette samtaler
KI-responser hentes korrekt fra valgt API-leverandør
Meldinger kan redigeres, også midt i en samtalehistorikk
Flere samtaler kan foregå parallelt

Overlevering

Koden er publisert i et git-repository med tilstrekkelig dokumentasjon
Installasjons- og konfigurasjonsinstruksjoner er komplette

Suksesskriterier

Systemet brukes regelmessig av skaperen
Teknisk kompleksitet er holdt på et minimum
Funksjonaliteten oppfyller alle definerte krav

11. Ordliste og referanser
Terminologi

KI: Kunstig intelligens, i dette tilfellet store språkmodeller
API: Application Programming Interface, grensesnitt for kommunikasjon mellom programvare
Token: Minste enhet av tekst som prosesseres av en språkmodell
SQLite: Lettvekts relasjonsdatabase som lagrer data i en enkelt fil
Systemprompt: Instruksjoner til KI-modellen som definerer hvordan den skal oppføre seg

Referansedokumenter

OpenAI API-dokumentasjon
PHP PSR-12 kodestandard
SQLite-dokumentasjon

Kontaktinformasjon

Prosjekteier og utvikler: [Kontaktinformasjon]
Git-repository: [URL til repository]

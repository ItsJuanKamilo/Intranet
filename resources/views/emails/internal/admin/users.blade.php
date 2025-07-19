@component('mail::message')
# Listado de Usuarios
### Usuarios Activos

Total de usuarios activos: {{ count($report->getArray() ) }}

@component('mail::table')
    | ID | Name         | Email                | Role       | Status   | Amount CLP       | Transactions | Department | Location     | Join Date   |
    |----|--------------|----------------------|------------|----------|------------------|--------------|------------|--------------|-------------|
    | 1  | John Doe     | john.doe@example.com | Admin      | Active   | 1,000,000        | 5            | IT         | Santiago     | 2020-01-15  |
    | 2  | Jane Smith   | jane.smith@example.com| User       | Inactive | 2,500,000        | 3            | HR         | Valparaiso   | 2019-03-22  |
    | 3  | Alice Johnson| alice.j@example.com  | Moderator  | Active   | 3,750,000        | 7            | Marketing  | Concepcion   | 2021-07-30  |
    | 4  | Bob Brown    | bob.brown@example.com| User       | Active   | 4,200,000        | 2            | Sales      | Antofagasta  | 2018-11-05  |
    | 5  | Carol White  | carol.white@example.com| Admin    | Inactive | 5,600,000        | 4            | Finance    | La Serena    | 2022-05-18  |
@endcomponent

@if($report->url_excel)
@component('mail::button', ['url' => $report->url_excel])
Descargar Excel
@endcomponent
@endif

@endcomponent

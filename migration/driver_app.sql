create table jos_driver_rate_xref
(
    id        int(11) unsigned auto_increment
        primary key,
    id_driver int(11) unsigned not null,
    id_rate   int(11) unsigned not null,
    rate      decimal(6, 2)    not null,
    constraint id_driver
        unique (id_driver, id_rate)
)
    charset = utf8;

create table jos_driver_rates
(
    id_rate      int auto_increment
        primary key,
    warehouse_id int                  not null,
    rate         decimal(6, 2)        not null,
    rate_driver  decimal(6, 2)        not null,
    name         varchar(255)         not null,
    orderby      int(5)               not null,
    is_gofor     tinyint(1) default 0 not null,
    comment      varchar(255)         not null
)
    charset = utf8;

create index is_gofor
    on jos_driver_rates (is_gofor);

create index name
    on jos_driver_rates (name);

create index orderby
    on jos_driver_rates (orderby);

create index warehouse_id
    on jos_driver_rates (warehouse_id);

create table jos_driver_rates_postalcodes
(
    id         int auto_increment
        primary key,
    id_rate    int         not null,
    postalcode varchar(10) not null
)
    charset = utf8;

create index id_rate
    on jos_driver_rates_postalcodes (id_rate, postalcode);

create index postalcode
    on jos_driver_rates_postalcodes (postalcode);

drop table  jos_vm_routes;

create table jos_vm_routes
(
    id                 int auto_increment
        primary key,
    driver_id          int                         not null,
    warehouse_id       int                         not null,
    datetime           datetime                    not null,
    username           varchar(255)                not null,
    destination        varchar(255)                not null,
    publish            enum ('0', '1') default '1' not null,
    hidden             set ('0', '1')  default '0' not null,
    map_image          varchar(255)                not null,
    scan_session_token varchar(32)                 not null
)
    charset = utf8;

create index driver_id
    on jos_vm_routes (driver_id, warehouse_id, datetime);

create index hidden
    on jos_vm_routes (hidden);

create index publish
    on jos_vm_routes (publish);

create index scan_session_token
    on jos_vm_routes (scan_session_token);

create index warehouse_id
    on jos_vm_routes (warehouse_id);

create table jos_vm_routes_history
(
    id_history int auto_increment
        primary key,
    id_route   int          not null,
    text       text         not null,
    username   varchar(255) not null,
    datetime   datetime     not null
)
    charset = utf8;

create index id_route
    on jos_vm_routes_history (id_route, datetime);

drop table jos_vm_routes_orders;

create table jos_vm_routes_orders
(
    id                   int auto_increment
        primary key,
    route_id             int                                  not null,
    order_id             int                                  not null,
    id_rate              int                                  not null,
    rate                 decimal(6, 2)                        not null,
    driver_rate          decimal(6, 2)                        not null,
    queue                varchar(1)                           not null,
    status               set ('0', '1', '2', '3') default '0' not null,
    billable             set ('0', '1')           default '1' not null,
    lat                  decimal(10, 6)                       not null,
    lng                  decimal(10, 6)                       not null,
    distance             varchar(255)                         not null,
    duration             varchar(255)                         not null,
    delivered_datetime   datetime                             not null,
    last_update_datetime datetime                             not null,
    distance_raw         int                                  not null,
    duration_raw         int                                  not null
)
    charset = utf8;

create index billable
    on jos_vm_routes_orders (billable);

create index id_rate
    on jos_vm_routes_orders (id_rate);

create index order_id
    on jos_vm_routes_orders (order_id, queue, status);

create index order_id_2
    on jos_vm_routes_orders (order_id);

create index route_id
    on jos_vm_routes_orders (route_id);

create table jos_vm_scanning_orders
(
    id         int(11) unsigned auto_increment
        primary key,
    session_id int(11) unsigned not null,
    order_id   int(11) unsigned not null
)
    charset = utf8;

create index order_id
    on jos_vm_scanning_orders (order_id);

create index session_id
    on jos_vm_scanning_orders (session_id);

create table jos_vm_scanning_sessions
(
    id                int(11) unsigned auto_increment
        primary key,
    token             varchar(32) not null,
    datetime_add      datetime    not null,
    datetime_modified datetime    not null,
    driver_id         int         not null
)
    charset = utf8;

create index datetime_modified
    on jos_vm_scanning_sessions (datetime_modified);

create index driver_id
    on jos_vm_scanning_sessions (driver_id);

create index token
    on jos_vm_scanning_sessions (token);

create table tbl_driver_queries
(
    id         int(11) unsigned auto_increment
        primary key,
    file       varchar(255)    not null,
    query      text            not null,
    error      text            not null,
    query_time decimal(16, 10) not null,
    rows_count int             not null,
    run_time   datetime        not null
)
    charset = utf8mb4;

create index run_time
    on tbl_driver_queries (run_time);

create table jos_vm_orders_qr
(
    id       int(11) unsigned auto_increment
        primary key,
    order_id int(11) unsigned not null,
    token    varchar(32)      not null
)
    charset = utf8;

create index order_id
    on jos_vm_orders_qr (order_id, token);
    
create table tbl_address_validation
(
    id       int(12) auto_increment
        primary key,
    order_id int(12) not null,
    score    int(3)  not null
)
    charset = latin1;

create index order_id
    on tbl_address_validation (order_id);

create table jos_vm_warehouse_info
(
    id             int auto_increment
        primary key,
    warehouse_id   int                   not null,
    person_name    varchar(255)          not null,
    company_name   varchar(255)          not null,
    street_number  varchar(255)          not null,
    street_name    varchar(255)          not null,
    city           varchar(255)          not null,
    state          varchar(255)          not null,
    zip            varchar(255)          not null,
    country        varchar(255)          not null,
    phone          varchar(255)          not null,
    lat            decimal(10, 6)        not null,
    lng            decimal(10, 6)        not null,
    warehouse_type smallint(2) default 1 not null,
    constraint warehouse_id_2
        unique (warehouse_id)
)
    charset = utf8;

create index warehouse_type
    on jos_vm_warehouse_info (warehouse_type);

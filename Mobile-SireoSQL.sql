-- Table: public.generic_line

-- DROP TABLE IF EXISTS public.generic_line;

CREATE TABLE IF NOT EXISTS public.generic_line
(
    id integer NOT NULL DEFAULT nextval('generic_line_id_seq'::regclass),
    name character varying(30) COLLATE pg_catalog."default" NOT NULL,
    descr text COLLATE pg_catalog."default",
    created timestamp with time zone,
    createdby character varying(30) COLLATE pg_catalog."default",
    modified timestamp with time zone,
    modifiedby character varying(30) COLLATE pg_catalog."default",
    geom geometry(MultiLineString,4326),
    CONSTRAINT generic_line_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.generic_line
    OWNER to postgres;

INSERT INTO public.generic_line(
	id, name, descr, created, createdby, modified, modifiedby, geom)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?);


-- Table: public.generic_point

-- DROP TABLE IF EXISTS public.generic_point;

CREATE TABLE IF NOT EXISTS public.generic_point
(
    id integer NOT NULL DEFAULT nextval('generic_point_id_seq'::regclass),
    name character varying(30) COLLATE pg_catalog."default" NOT NULL,
    descr text COLLATE pg_catalog."default",
    created timestamp with time zone,
    createdby character varying(30) COLLATE pg_catalog."default",
    modified timestamp with time zone,
    modifiedby character varying(30) COLLATE pg_catalog."default",
    geom geometry(Point,4326),
    CONSTRAINT generic_point_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.generic_point
    OWNER to postgres;

INSERT INTO public.generic_point(
	id, name, descr, created, createdby, modified, modifiedby, geom)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?);


-- Table: public.generic_poly

-- DROP TABLE IF EXISTS public.generic_poly;

CREATE TABLE IF NOT EXISTS public.generic_poly
(
    id integer NOT NULL DEFAULT nextval('generic_poly_id_seq'::regclass),
    name character varying(30) COLLATE pg_catalog."default" NOT NULL,
    descr text COLLATE pg_catalog."default",
    created timestamp with time zone,
    createdby character varying(30) COLLATE pg_catalog."default",
    modified timestamp with time zone,
    modifiedby character varying(30) COLLATE pg_catalog."default",
    geom geometry(MultiPolygon,4326),
    CONSTRAINT generic_poly_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.generic_poly
    OWNER to postgres;

INSERT INTO public.generic_poly(
	id, name, descr, created, createdby, modified, modifiedby, geom)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?);

-- Table: public.groups

-- DROP TABLE IF EXISTS public.groups;

CREATE TABLE IF NOT EXISTS public.groups
(
    id integer NOT NULL DEFAULT nextval('groups_id_seq'::regclass),
    name character varying(30) COLLATE pg_catalog."default" NOT NULL,
    descr text COLLATE pg_catalog."default",
    CONSTRAINT groups_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.groups
    OWNER to postgres;

INSERT INTO public.groups(
	id, name, descr)
	VALUES (?, ?, ?);

-- Table: public.pages

-- DROP TABLE IF EXISTS public.pages;

CREATE TABLE IF NOT EXISTS public.pages
(
    id integer NOT NULL DEFAULT nextval('pages_id_seq'::regclass),
    name character varying(50) COLLATE pg_catalog."default" NOT NULL,
    descr text COLLATE pg_catalog."default",
    url character varying(255) COLLATE pg_catalog."default" NOT NULL,
    group_id integer NOT NULL,
    CONSTRAINT pages_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.pages
    OWNER to postgres;

INSERT INTO public.pages(
	id, name, descr, url, group_id)
	VALUES (?, ?, ?, ?, ?);

-- Table: public.user_group_link

-- DROP TABLE IF EXISTS public.user_group_link;

CREATE TABLE IF NOT EXISTS public.user_group_link
(
    id integer NOT NULL DEFAULT nextval('user_group_link_id_seq'::regclass),
    group_id integer NOT NULL,
    user_id integer NOT NULL,
    CONSTRAINT user_group_link_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.user_group_link
    OWNER to postgres;

INSERT INTO public.user_group_link(
	id, group_id, user_id)
	VALUES (?, ?, ?);

-- Table: public.users

-- DROP TABLE IF EXISTS public.users;

CREATE TABLE IF NOT EXISTS public.users
(
    id integer NOT NULL DEFAULT nextval('users_id_seq'::regclass),
    firstname character varying(25) COLLATE pg_catalog."default",
    lastname character varying(25) COLLATE pg_catalog."default",
    username character varying(25) COLLATE pg_catalog."default",
    password character varying(255) COLLATE pg_catalog."default",
    validationcode character varying(255) COLLATE pg_catalog."default",
    email character varying(100) COLLATE pg_catalog."default",
    comments text COLLATE pg_catalog."default",
    joined date,
    last_login date,
    active smallint DEFAULT 0,
    CONSTRAINT users_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.users
    OWNER to postgres;

INSERT INTO public.users(
	id, firstname, lastname, username, password, validationcode, email, comments, joined, last_login, active)
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);



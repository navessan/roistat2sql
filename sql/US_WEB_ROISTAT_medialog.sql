
CREATE TABLE [dbo].[US_WEB_ROISTAT_CALLS](
	[US_WEB_ROISTAT_CALLS_ID] [int] IDENTITY(1,1) NOT NULL,
	[MEDIALOG_CALL_ID] [int] NULL,
	[call_date] [datetime] NULL,
	[id] [int] NULL,
	[callee] [varchar](50) NULL,
	[caller] [varchar](50) NULL,
	[duration] [int] NULL,
	[status] [varchar](50) NULL,
	[date] [varchar](50) NULL,
	[visit_id] [varchar](50) NULL,
	[order_id] [varchar](50) NULL,
	[static_source] [text] NULL,
	[system_name] [varchar](250) NULL,
	[display_name] [varchar](250) NULL,
	[icon_url] [varchar](250) NULL,
	[utm_source] [text] NULL,
	[utm_medium] [text] NULL,
	[utm_campaign] [text] NULL,
	[utm_term] [text] NULL,
	[utm_content] [text] NULL,
	[openstat] [varchar](50) NULL,
	[comment] [text] NULL,
	[visit] [text] NULL,
	[order_text] [text] NULL,
	[link] [text] NULL,
	[waiting_time] [int] NULL,
	[answer_duration] [int] NULL,
	[KRN_CREATE_DATE] [datetime] NULL,
	[KRN_CREATE_USER_ID] [int] NULL,
	[KRN_MODIFY_DATE] [datetime] NULL,
	[KRN_MODIFY_USER_ID] [int] NULL,
	[KRN_CREATE_DATABASE_ID] [int] NULL,
	[KRN_MODIFY_DATABASE_ID] [int] NULL,
	[KRN_GUID] [varchar](36) NULL,
 CONSTRAINT [PK_US_WEB_ROISTAT_CALLS] PRIMARY KEY CLUSTERED 
(
	[US_WEB_ROISTAT_CALLS_ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO

CREATE TABLE [dbo].[US_WEB_ROISTAT_ORDERS](
	[US_WEB_ROISTAT_ORDERS_ID] [int] IDENTITY(1,1) NOT NULL,
	[id] [varchar](50) NULL,
	[url] [varchar](50) NULL,
	[source_type] [varchar](50) NULL,
	[creation_date] [datetime] NULL,
	[creation_date_text] [varchar](50) NULL,
	[update_date] [datetime] NULL,
	[update_date_text] [varchar](50) NULL,
	[revenue] [int] NULL,
	[cost] [int] NULL,
	[visit_id] [varchar](50) NULL,
	[custom_fields] [text] NULL,
	[custom_manager] [varchar](50) NULL,
	[custom_roistat] [varchar](50) NULL,
	[custom_status_name] [varchar](50) NULL,
	[status] [text] NULL,
	[status_id] [varchar](50) NULL,
	[status_type] [varchar](50) NULL,
	[status_name] [varchar](50) NULL,
	[visit] [text] NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO

CREATE TABLE [dbo].[US_WEB_ROISTAT_SOURCES](
	[US_WEB_ROISTAT_SOURCES_ID] [int] IDENTITY(1,1) NOT NULL,
	[source] [varchar](250) NULL,
	[name] [varchar](250) NULL,
	[type] [varchar](50) NULL,
	[level_id] [int] NULL,
	[icon] [varchar](250) NULL
) ON [PRIMARY]

GO

CREATE TABLE [dbo].[US_WEB_ROISTAT_SOURCES_tmp](
	[source] [varchar](250) NULL,
	[name] [varchar](250) NULL,
	[type] [varchar](50) NULL,
	[level_id] [int] NULL,
	[icon] [varchar](250) NULL
) ON [PRIMARY]

GO

CREATE TABLE [dbo].[US_WEB_ROISTAT_VISITS](
	[US_WEB_ROISTAT_VISITS_ID] [int] IDENTITY(1,1) NOT NULL,
	[id] [varchar](50) NULL,
	[visit_date] [datetime] NULL,
	[first_id] [varchar](50) NULL,
	[date] [varchar](50) NULL,
	[landing_page] [varchar](250) NULL,
	[host] [varchar](250) NULL,
	[google_client_id] [text] NULL,
	[metrika_client_id] [text] NULL,
	[ip] [varchar](50) NULL,
	[roistat_param1] [varchar](250) NULL,
	[roistat_param2] [varchar](250) NULL,
	[roistat_param3] [varchar](250) NULL,
	[roistat_param4] [varchar](250) NULL,
	[roistat_param5] [varchar](250) NULL,
	[device] [text] NULL,
	[source] [text] NULL,
	[system_name] [text] NULL,
	[display_name] [text] NULL,
	[icon_url] [varchar](250) NULL,
	[utm_source] [text] NULL,
	[utm_medium] [text] NULL,
	[utm_campaign] [text] NULL,
	[utm_term] [text] NULL,
	[utm_content] [text] NULL,
	[openstat] [varchar](50) NULL,
	[geo] [text] NULL,
	[country] [varchar](250) NULL,
	[region] [varchar](250) NULL,
	[city] [varchar](250) NULL,
	[geo_icon_url] [varchar](250) NULL,
	[country_iso] [varchar](50) NULL,
	[order_ids] [text] NULL,
	[KRN_CREATE_DATE] [datetime] NULL,
	[KRN_CREATE_USER_ID] [int] NULL,
	[KRN_MODIFY_DATE] [datetime] NULL,
	[KRN_MODIFY_USER_ID] [int] NULL,
	[KRN_CREATE_DATABASE_ID] [int] NULL,
	[KRN_MODIFY_DATABASE_ID] [int] NULL,
	[KRN_GUID] [varchar](36) NULL,
 CONSTRAINT [PK_US_WEB_ROISTAT_VISITS] PRIMARY KEY CLUSTERED 
(
	[US_WEB_ROISTAT_VISITS_ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]


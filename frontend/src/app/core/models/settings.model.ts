export interface GeneralSettings {
  company_name: string;
  company_email: string;
  timezone: string;
  date_format: string;
  theme: string;
  logo?: string;
  favicon?: string;
}

export interface EmailSettings {
  mail_driver: string;
  mail_host: string;
  mail_port: string | number;
  mail_username: string;
  mail_from_address: string;
  mail_encryption: string;
}

export interface Settings extends GeneralSettings, EmailSettings {}

#!/usr/bin/perl -w
use strict;
my %CORE_DB_CONFIG = (
   user => 'mpid_to_bc',
   pass => '',
   host => '192.168.4.205',
   db   => 'monipla_account',
);
my %BC_DB_CONFIG = (
   user => 'bc_deploy',
   pass => 'mmwxn0qb2',
   host => '192.168.4.242',
   db   => 'db_brandco',
);

my $tbl = ($ARGV[0] or 'social_likes');
system "echo start";
system "date";

# Step1. Download data as a CSV file.

system
  "echo 'SELECT id, user_id, social_media_id, like_id FROM $tbl' | " .
  "mysql -u $CORE_DB_CONFIG{user} " .
  "-p$CORE_DB_CONFIG{pass} " .
  "-h $CORE_DB_CONFIG{host}  " .
  "$CORE_DB_CONFIG{db} | " .
  "perl -anle 'print join (\",\", map {my \$col = \$_; \$col =~ s/\"/\\\"/g; \$col} split /\t/)' > $tbl.csv" and die('あぼーん ' . $!);

# Step2. Load the CSV file into a SQL table.

my $mysql = "mysql -u $BC_DB_CONFIG{user} -p$BC_DB_CONFIG{pass} -h $BC_DB_CONFIG{host} $BC_DB_CONFIG{db}";
my ($truncate, $drop_index, $load, $create_index) = (
  "TRUNCATE TABLE $tbl",
  "DROP INDEX social_likes_user_id_like_id_index ON social_likes",
  "LOAD DATA LOCAL INFILE '$tbl.csv' INTO TABLE $tbl FIELDS TERMINATED BY ',' ENCLOSED BY '\\\"' IGNORE 1 LINES",
  "CREATE INDEX social_likes_user_id_index ON social_likes(user_id, like_id)",
  # 上記の書き込みで、Buffer poolが汚染されてしまうので、後続のバッチで必要なデータをプリフェッチします。
  "SELECT COUNT(*) FROM brands WHERE del_flg = 0",
  "SELECT COUNT(*) FROM cp_users WHERE del_flg = 0",
  "SELECT COUNT(*) FROM cps WHERE del_flg = 0",
  "SELECT COUNT(*) FROM cp_actions WHERE del_flg = 0",
  "SELECT COUNT(*) FROM cp_action_groups WHERE del_flg = 0",
  "SELECT COUNT(*) FROM cp_user_action_statuses WHERE del_flg = 0",
  "SELECT COUNT(*) FROM growth_user_stats WHERE created_at = 0"
);
system "echo \"$truncate; $load\" | $mysql" and die ('あぼーん');

# Termination.

unlink "$tbl.csv";
system "echo end";
system "date";
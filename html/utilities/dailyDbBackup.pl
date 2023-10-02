use Time::Local;
$ENV{TZ} = ':/usr/share/zoneinfo/US/Pacific';
($sec,$min,$hour,$mday,$mon,$year,$wday,$yday) = localtime(time());
$year += 1900;

# MONTH IS REPORTED 0-11....  I CHANGED THIS ONLY ON NOV 26 02, MEANING
# THAT ALL THE BACKUPS BEFORE THAT WERE OBO.
$mon += 1;

$mysqldumpPath = "/usr/local/mysql/bin/";
$backupPath = "/var/www/html/gfpDatabaseBackup/";
$remoteBackupServer = "gerke-oshea.ucsf.edu";
$remoteBackupPath = "/home/lcgerke/gfpDatabaseBackup/";

$outFile = "gfpDatabaseBackupY$year\_M$mon\_D$mday\_H$hour\_M$min\_S$sec\_PDT";
$command = $mysqldumpPath."mysqldump -B gfp > ".$backupPath.$outFile;
system($command);
$command2 = "scp ".$backupPath.$outFile." ".$remoteBackupServer.":".$remoteBackupPath.$outFile;
system($command2);

#$command2 = " scp /var/www/html/gfpDatabaseBackup/gfpDatabaseBackupY2002_M7_D22_H16_M20_S53_PDT  ucsf-205-202.ucsf.edu:/home/lcgerke/gfpDatabaseBackup/gfpDatabaseBackupY2002_M7_D22_H16_M20_S53_PDT";

#print $command2."\n";



#print $command;


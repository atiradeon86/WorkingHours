SELECT FORMAT (start_date, 'yyyy-MM-dd') as Workday, FORMAT(cast(StartTime as time), N'hh\.mm') AS StartTime, FORMAT(cast(EndTime as time), N'hh\.mm') AS RealEndTime,
ROUND ( CAST(DATEDIFF (second,CAST(StartTime AS DATETIME),CAST(EndTime AS DATETIME)) AS FLOAT)/3600,2) AS WorkingHours
/*FORMAT(cast(DATEADD(MINUTE, -30, EndTime) AS TIME), N'hh\.mm') AS EndTimeCalculated*/ 

from dbo.WorkingHours
WHERE YEAR(start_date) ='2023' AND MONTH(start_date) ='12'

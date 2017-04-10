if [ $1 = 'stop' ]
    then
      NAME=`screen -list | grep 'ExusMultibotInstance' | cut -d . -f1`
      kill -3 $NAME

      NAZWA=`screen -list | grep 'ExusMultibot' | cut -d . -f1`
      kill -3 $NAME
    fi

if [ $1 = 'start' ]
    then
	     screen -A -m -d -S ExusMultibot php Core/core.php --startmode commands --lang pl
    fi

export SGE_ROOT="/opt/sge"

if [ -x $SGE_ROOT/util/arch ]; then
  export SGE_ARCH=`$SGE_ROOT/util/arch`
  DEFAULTMANPATH=`$SGE_ROOT/util/arch -m`
  MANTYPE=`$SGE_ROOT/util/arch -mt`

  export SGE_CELL="default"
  export SGE_CLUSTER_NAME="p6444"
  unset SGE_QMASTER_PORT
  export SGE_EXECD_PORT
  export DRMAA_LIBRARY_PATH="/opt/sge/lib//libdrmaa.so"

  # library path setting required only for architectures where RUNPATH is not supported
  if [ -d $SGE_ROOT/$MANTYPE ]; then
     if [ $?MANPATH == 1 ]; then
        export MANPATH=$SGE_ROOT/${MANTYPE}:$MANPATH
     else
        export MANPATH=$SGE_ROOT/${MANTYPE}:$DEFAULTMANPATH
     fi
  fi

  export PATH=$SGE_ROOT/bin:$SGE_ROOT/bin/$SGE_ARCH:$PATH

  if [ -d $SGE_ROOT/lib/$SGE_ARCH ]; then
    case $SGE_ARCH in
      sol*|lx*|hp11-64)
      ;;
      *)
        shlib_path_name = `$SGE_ROOT/util/arch -lib`
        if [ `eval echo '$?'$shlib_path_name` ]; then
           old_value = `eval echo '$'$shlib_path_name`
           export $shlib_path_name="$SGE_ROOT/lib/$SGE_ARCH:$old_value"
        else
           export $shlib_path_name=$SGE_ROOT/lib/$SGE_ARCH
        fi
        unset shlib_path_name old_value
      ;;
    esac
  fi

  unset DEFAULTMANPATH MANTYPE
else
  unset SGE_ROOT
fi

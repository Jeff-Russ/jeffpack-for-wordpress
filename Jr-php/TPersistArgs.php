<?php
namespace Jr;
/**
 * TPersistArgs is a trait to give your class a better way to 
 * parse accept method arguments and persist data between calls to avoid
 * nested callback and reduce the number or required arguments, keeping your code DRY.
 * 
 * @package     JeffPack
 * @subpackage  General PHP Libraries
 * @access      public
 * @author      Jeff Russ
 * @copyright   2016 Jeff Russ
 * @license     GPL-2.0
 */
if ( ! trait_exists('TPersistArgs')) {
	/**
	 * TPersistArgs is a trait to give your class a better way to 
	 * parse accept method arguments and persist data between calls to avoid
	 * nested callback and reduce the number or required arguments, keeping your code DRY.
	 * 
	 * Most methods are declared protected and meant to be called from an inheriting 
	 * classes methods. 
	 * 
	 * You class's methods would accept "&$args", either as it's only argument or it's
	 * last argument, acting as a sort of "splat argument" with named parameters. 
	 * 
	 * There will also be a $this->args array property in your object that mirrors 
	 * the most recently received arguments and any other related data. 
	 * 
	 * The $args sent to a method, if found to be an array, can have it's key/value 
	 * merged into the $this->args. If it's a single value it's assigned a key. 
	 * Since $args is passed by reference it's updated and syncronized with 
	 * $this->args meaning caller can use the modified variable in a follow-up
	 * method call to provide a sort of context. If the argument is not supplied, 
	 * values from $this-args is used instead. 
	 */
	trait TPersistArgs {

		/** @var array olds whatever variables are needed between calls */
		public $args = array();

		#### Helpers ##############################################################

		/**
		* Get a single value from $args or $this->args without writing to either.
		* 
		* If $args is an array, the $key is looked up and the value is returned.
		* If $args is a single value, that value is returned. 
		* If $args is null or the key does not exist, $this->args[$key] is returned
		* without first checking if the key exists. 
		* 
		* An error will be thrown if the $key is not found or therefore this method is 
		* useful if you need to halt things when the value can't be found. 
		*
		* @param  mixed   $args is array or anything else
		* @param  mixed   $key is existing or hypothetical array key
		* @return mixed   $args[$key], $this->args[$key], or just $args
		* @access protected
		*/
		protected function getArg($args, $key)
		{
			if ( is_array($args) ):
				if ( array_key_exists($key, $args) ):
					return $args[$key];
				// elseif ( array_key_exists($key, $this->args) ):
				else: # let it get error
					return $this->args[$key];
				endif;
			elseif ( $args !== null ):
				return $args;
			else:
				// if ( array_key_exists($key, $this->args) ) 
					# nm, let it error
					return $this->args[$key];
			endif;
		}

		/**
		* Get a single value from $args only (not checking $this->args) and without 
		* writing to either.
		* 
		* This method is useful when you create a method which needs a value to be 
		* set in that call without falling back on data from a previous call. 
		* 
		* If $args is an array, the $key is looked up and the value is returned.
		* If $args is a single value, that value is returned. 
		* If $args is null or the key does not exist, null or some other value is 
		* return, which can be set by the optional third argument. 
		*
		* @param  mixed   $args is array or anything else
		* @param  mixed   $key is existing or hypothetical array key
		* @param  mixed   $default (optional) return if value can't be found (defaults to null)
		* @return mixed   $args[$key] or $defaults
		* @access protected
		*/
		protected function getPassedArg($args, $key, $default=null)
		{
			if ( is_array($args) ):
				if ( array_key_exists($key, $args) ):
					return $args[$key];
				else:
					return $default;
				endif;
			elseif ( $args === null ):
				return $default;
			else:
				return $args;
			endif;
		}

		/**
		* This static method is useful for intializing an array from an unknown 
		* variable. It does not modify the object or it's arguments and is use 
		* only for it's return usually to assign to an array variable.  
		* 
		* A new array is returned from the contents of $args which 
		* could be an array itself, a single value or null. The second argument 
		* defines a key to be created if a value not associated with a string 
		* key is found. The return is always an array.
		* 
		* If $args is null an empty array is return and $key is not used.
		* If $args is not an array and not null, that value is added to a new 
		* array at $key and the array is return.
		* If $args is an array lacking $key but has a value at $args[0], 
		* that value is moved to $args[$key] and $args[0] is deleted.
		* If $args is an array but there is no $args[0], it's returned as is. 
		*
		* @param  mixed   $args is array or anything else
		* @param  mixed   $key is a hypothetical array key, usually a string
		* @return array   a new array or a copy of $args if it it an array.
		* @access public
		*/
		static public function toArrayAsKey($args, $key) #PUBLIC!
		{
			if (! is_array($args) ): 
				if ($args !== null):
					$args = array($key => $args);
				else:
					$args = array();
				endif;
			elseif (array_key_exists(0, $args) && !array_key_exists($key, $args) ):
				$args[$key] = $args[0];
				unset($args[0]);
			endif;
			return $args;
		}

		/**
		* This method gets a desired value from either the $args parameter or 
		* $this->args and synchronizes them as a side effect. 
		* 
		* This method modifies both $this->args and $args to mirror each other
		* and return a desired value from either of them or a default value if 
		* not found (defaults to null). $key must be provided to determine which 
		* element is to be returned
		* 
		* If $args anything not an array but not null, it's assigned to both 
		* $args at $key and returned. 
		* 
		* If $args is an array it's merged in both directions with $this->args 
		* overriding any matching keys found in $this->args. If $key is found 
		* in either, it's value is returned, or else $default is returned. 
		* 
		* @param  mixed   $args is array or anything else
		* @param  mixed   $key is a hypothetical array key, usually a string
		* @param  mixed   $default (optional) return if value can't be found (defaults to null)
		* @return mixed   the value finally residing in args[$key] or $default
		* @access protected
		*/
		protected function getArgAndSync(&$args, $key, $default=null)
		{
			if ( is_array($args) ):
				$this->args = array_merge($this->args, $args);
			elseif ( $args !== null ):
				$this->args[$key] = $args;
			endif;
			$args = $this->args;
			if ( array_key_exists($key, $args) )
				return $args[$key];
			else
				return $default;
		}
		/**
		* This method updates $this->args from the argument values and returns
		* it even if no change occured. It does not modify either argument so 
		* if you'd like to synchronize them you should use this method's return 
		* to re-assign the local variable(s) you passed in. There are three modes 
		* you can use this method depending on the type and presence of the 
		* second argument. 
		* 
		* SIMPLE MERGE MODE:
		* 
		* If $arg1 or $arg2 is provided as an array and the other is null,
		* it is merged into $this->args overriding any matching keys in 
		* $this->args.  
		* 
		* KEY MODE:
		* 
		* When you are uncertain that the first argument is an array you should 
		* provide a key as the second argument which will be used to assign 
		* the value of $arg1 to $this->args. 
		* 
		* If $arg1 is non-null and not an array it will be added to $this->args
		* at using $arg2 as a key or or pushed to the end of the $this->args 
		* at a new int index if $arg2 is not usable as a key.
		* 
		* TWO ARRAY MERGE MODE:
		* 
		* When you are certain $arg1 is either an array or null you can either leave 
		* off the second argument or provide a second array where both will be merged 
		* into $this-args. 
		* 
		* If both arguments are provided and are arrays they are combined,
		* with $arg2 overriding any matching keys in $arg1, then this is merged 
		* into $this->args overriding any matching keys in $this->args.  
		* 
		* @param  mixed   $arg1 expected to be array or non-null value
		* @param  mixed   $arg2 expected to be array or key (but optional)
		* @return array   $this->args
		* @access protected
		*/
		protected function mergeArgs($arg1, $arg2=null) {
			if ( is_array($arg1) )
			{
				if ( is_array($arg2) ) # merge both arrays
					$this->args = array_merge($this->args, array_merge($arg1, $arg2) );
				else                   # this ignores arg2
					$this->args = array_merge($this->args, $arg1);
			}
			elseif ( $arg1 === null )
			{
				if ( is_array($arg2) ) # merge arg2 to $this and ignore arg1
					$this->args = array_merge($this->args, $arg2);
			}
			elseif ( is_int($arg2) || is_string($arg2) )
			{
				# we know $arg1 is a non-null non-array and $arg2 can be a key
				$this->args[$key] = $arg1;
			}
			else # we know $arg1 is a non-null non-array but $arg2 can't be key
			{
				if ( is_array($arg2) ) $this->args = array_merge($this->args, $arg2);
				$this->args[] = $arg1;
			}
			return $this->args;
		}
	}
}

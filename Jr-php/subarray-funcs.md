
# SUB-ARRAY FUNCTIONS 1: ROTATING

ISSUE: you want the value of 'subkey' below to be the keys of the array.
Also (although might not tell simply by looking) the current keys are 
error codes and you want to rotate them into the subarray elements under
the key 'code', replacing the 'subkey' element which will not be the 
outer-most keys.

    $by_code = array(
      404 => ['subkey'=>'subkey-val-DOH','subkey2'=>'subkey2-val-1'],
      302 => ['subkey'=>'subkey-val-one','subkey2'=>'subkey2-val-2'],
      418 => ['subkey'=>'subkey-val-DOH','subkey2'=>'subkey2-val-3'],
      422 => ['subkey'=>'subkey-val-two','subkey2'=>'subkey2-val-4'],
    );
    function pretty_print($a){echo json_encode($a,JSON_PRETTY_PRINT)."\n<hr>\n";}

## array\_rotate()

array\_rotate() is like array\_flip() only it's for Two-dimensional arrays.
There can be more than two dimensions but the value of $subkey can't be
another array and must be a valid key.

    pretty_print( array_rotate($by_code, 'subkey', 'code') );

output:

    [
      "subkey-val-DOH"=>[ "subkey2"=>"subkey2-val-3", "code"=>404 ],
      "subkey-val-one"=>[ "subkey2"=>"subkey2-val-2", "code"=>302 ]
      "subkey-val-two"=>[ "subkey2"=>"subkey2-val-4", "code"=>422 ],
    ]

## array\_rotate\_category()

array\_rotate\_category() is like array\_rotate() only it avoids 
clobbering duplicate values of $subkey by creating "category" array 
containters for each possible value of $subkey. This additional 
array container will be created even if there is only one sub-key 
with a particular value (and would not have caused a collision). 

    pretty_print( array_rotate_category($by_code, 'subkey', 'code') );
output:

    [
      "subkey-val-DOH"=>[
        0=>[ "subkey2"=>"subkey2-val-1", "code"=>404 ],
        1=>[ "subkey2"=>"subkey2-val-3", "code"=>404 ]
      ],
      "subkey-val-one"=>[
        0=>[ "subkey2"=>"subkey2-val-2", "code"=>302 ]
      ],
      "subkey-val-two"=>[
        0=>[ "subkey2"=>"subkey2-val-4", "code"=>422 ]
      ]
    ]

## array\_rotate\_jagged()

array\_rotate\_jagged() is an adaptive version of array\_rotate\_category()
meaning it will create a category only if a collision would occur. 
This means that the final array returned is "jagged" meaning each 
element might have a diffent dimensional depth (could be a single array
or an array of arrays).

    pretty_print( array_rotate_jagged($by_code, 'subkey', 'code') ); 
output:

    [
      "subkey-val-DOH"=>[
        0=>[ "subkey2"=>"subkey2-val-1", "code"=>404 ]
        1=>[ "subkey2"=>"subkey2-val-3", "code"=>418 ]
      ],
      "subkey-val-one"=>[ "subkey2"=>"subkey2-val-2", "code"=>302 ],
      "subkey-val-two"=>[ "subkey2"=>"subkey2-val-4", "code"=>422 ]
    ]

The following three methods apply similar processes seen in array\_rotate(), 
array\_rotate\_category(), and array_rotate\_jagged() but do so for every inner key as well as the outermost key. 

The returned array is an array where each key is the result of a search for each subkey, only showing the LAST result.

## create\_rotations()

create\_rotations() is the en masse version of array\_rotate().

    pretty_print( create_rotations($by_code, 'code') );
output:

    [
        "code"=>[
            404=>[ "subkey"=>"subkey-val-DOH", "subkey2"=>"subkey2-val-1" ],
            302=>[ "subkey"=>"subkey-val-one", "subkey2"=>"subkey2-val-2" ],
            418=>[ "subkey"=>"subkey-val-DOH", "subkey2"=>"subkey2-val-3" ],
            422=>[ "subkey"=>"subkey-val-two", "subkey2"=>"subkey2-val-4" ]
        ],
        "subkey"=>[
            "subkey-val-DOH"=>[ "subkey2"=>"subkey2-val-3", "code"=>404 ],
            "subkey-val-one"=>[ "subkey2"=>"subkey2-val-2", "code"=>302 ],
            "subkey-val-two"=>[ "subkey2"=>"subkey2-val-4", "code"=>422 ]
        ],
        "subkey2"=>[
            "subkey2-val-1"=>[ "subkey"=>"subkey-val-DOH", "code"=>404 ],
            "subkey2-val-2"=>[ "subkey"=>"subkey-val-one", "code"=>302 ],
            "subkey2-val-3"=>[ "subkey"=>"subkey-val-DOH", "code"=>418 ],
            "subkey2-val-4"=>[ "subkey"=>"subkey-val-two", "code"=>422 ]
        ]
    ]

# SUB-ARRAY FUNCTIONS 2: SORTING

## create\_sorts()

create\_sorts() is the en masse version of array\_rotate\_category().

NOTE: The primary key (if provided) is still "array-ified" when the 
lookup table sort is created for it EVEN though collisions are 
not possible. This is so you have a universal way of dealing with 
lookups (you will always the same depths of arrays.) In other words
even thought there should be only one "result" if you search by 
primary key, you will get an array with one value.  

    pretty_print( create_sorts($by_code, 'code') );

output:

    [
        "code"=>[
            404=>[
                0=>[ "subkey"=>"subkey-val-DOH", "subkey2"=>"subkey2-val-1" ]
            ],
            302=>[
                0=>[ "subkey"=>"subkey-val-one", "subkey2"=>"subkey2-val-2" ]
            ],
            418=>[
                0=>[ "subkey"=>"subkey-val-DOH", "subkey2"=>"subkey2-val-3" ]
            ],
            422=>[
                0=>[ "subkey"=>"subkey-val-two", "subkey2"=>"subkey2-val-4" ]
            ]
        ],
        "subkey"=>[
            "subkey-val-DOH"=>[
                0=>[ "subkey2"=>"subkey2-val-1", "code"=>404 ],
                1=>[ "subkey2"=>"subkey2-val-3", "code"=>418 ]
            ],
            "subkey-val-one"=>[
                0=>[ "subkey2"=>"subkey2-val-2", "code"=>302 ]
            ],
            "subkey-val-two"=>[
                0=>[ "subkey2"=>"subkey2-val-4", "code"=>422 ]
            ]
        ],
        "subkey2"=>[
            "subkey2-val-1"=>[
                0=>[ "subkey"=>"subkey-val-DOH", "code"=>404 ]
            ],
            "subkey2-val-2"=>[
                0=>[ "subkey"=>"subkey-val-one", "code"=>302 ]
            ],
            "subkey2-val-3"=>[
                0=>[ "subkey"=>"subkey-val-DOH", "code"=>418 ]
            ],
            "subkey2-val-4"=>[
                0=>[ "subkey"=>"subkey-val-two", "code"=>422 ]
            ]
        ]
    ]

## create\_sorts\_jagged()

create_sorts\_jagged() is the en masse version of array\_rotate\_jagged().

    pretty_print( create_sorts_jagged($by_code, 'code') ); 
output:

    [
        "code"=>[
            404=>[ "subkey"=>"subkey-val-DOH", "subkey2"=>"subkey2-val-1" ],
            302=>[ "subkey"=>"subkey-val-one", "subkey2"=>"subkey2-val-2" ],
            418=>[ "subkey"=>"subkey-val-DOH", "subkey2"=>"subkey2-val-3" ],
            422=>[ "subkey"=>"subkey-val-two", "subkey2"=>"subkey2-val-4" ]
        ],
        "subkey"=>[
            "subkey-val-DOH"=>[
                0=>[ "subkey2"=>"subkey2-val-1", "code"=>404 ],
                1=>[ "subkey2"=>"subkey2-val-3", "code"=>418 ]
            ],
            "subkey-val-one"=>[ "subkey2"=>"subkey2-val-2", "code"=>302 ],
            "subkey-val-two"=>[ "subkey2"=>"subkey2-val-4", "code"=>422 ]
        ],
        "subkey2"=>[
            "subkey2-val-1"=>[ "subkey"=>"subkey-val-DOH", "code"=>404 ],
            "subkey2-val-2"=>[ "subkey"=>"subkey-val-one", "code"=>302 ],
            "subkey2-val-3"=>[ "subkey"=>"subkey-val-DOH", "code"=>418 ],
            "subkey2-val-4"=>[ "subkey"=>"subkey-val-two", "code"=>422 ]
        ]
    ]

## create\_sorts\_jagged()

create\_sorts\_jagged() is the en masse version of array\_rotate\_jagged().

    pretty_print( subarray_search($by_code, 'subkey', 'subkey-val-DOH') );

output:

    [
        404,
        418
    ]


## create\_sorts\_jagged()

create\_sorts\_jagged() is the en masse version of array\_rotate\_jagged(). 

    echo subarray_find($by_code, 'subkey', 'subkey-val-DOH');
output:

    404

# SUB-ARRAY FUNCTIONS 3: FLATTENING AND MERGING

```php
$argv = [
    'arg0 value',
    'arg1 value',
    'arg2 value',
    [   'arg3[0] key' =>'arg3[0] value',
        'arg3[1] key' =>'arg3[1] value',
    ],
    'arg4 value',
    [   'arg5[0] value',
        'arg5[1] key'=>'arg5[1] value'
    ]
];
```

## array\_flatten()

array\_flatten() makes a two-dimensional into one-dimension while retaining 
sequential order. Both the top and second level arrays have their integer 
keys __re-indexed to in consecutive sequence__.  

example using `$argv` above:  

```php
$arr = array_flatten($argv, true, false);
echo json_encode($arr, JSON_PRETTY_PRINT);
```
output:

    {
        "0": "arg0 value",
        "1": "arg1 value",
        "2": "arg2 value",
        "arg3[0] key": "arg3[0] value",
        "arg3[1] key": "arg3[1] value",
        "4": "arg4 value",
        "5": "arg5[0] value",
        "arg5[1] key": "arg5[1] value"
    }
If argument 2 $safe is set to true, an array will not be returned if 
duplicate string keys are found. If argument 2 $fatal is false, false will be
returned in this case. If it is not false a fatal error will occur. If fatal 
is set to a string, it will be used as the label for the error message.

## merge\_subarrays()

merge\_subarrays() removes sub-arrays from an array and merges them together
into another array. The return value is an array of the two arrays with they 
keys `'top'` and `'sub'`. __The contents of 'top' are not re-indexed and there may be gaps in integer keys.__  

example using `$argv` above:  

```php
$arr = merge_subarrays($argv, true, false);
echo json_encode($arr, JSON_PRETTY_PRINT);
```
output:  

    {
        "top": {
            "0": "arg0 value",
            "1": "arg1 value",
            "2": "arg2 value",
            "4": "arg4 value"
        },
        "sub": {
            "arg3[0] key": "arg3[0] value",
            "arg3[1] key": "arg3[1] value",
            "0": "arg5[0] value",
            "arg5[1] key": "arg5[1] value"
        }
    }
If second argument $safe is set to true, duplicate keys cannot be overwritten and instead, false is returned.

## argv\_merge\_vars()

Similar to both func_get_args() and merge_subarrays() only not needing the 
first argument as it is the caller's arguments (from a function or method).
The return is the same as merge_subarrays() only `'top'` is called `'argv'` and `'sub'` is called `'vars'`

An example:  

```php
function test() {
    extract(argv_and_vars());
    echo "argv: ".json_encode($argv, JSON_PRETTY_PRINT);
    echo "vars: ".json_encode($vars, JSON_PRETTY_PRINT);
    extract($vars);
    echo '$var1 '.$var1."\n";
    echo '$var2 '.$var2."\n";
    echo '$var3 '.$var3."\n";
}

$named_args = array('var1'=>'var1 value', 'var2' =>'var2 value');
$one_more = array('var3'=>'var3 value');
test('arg0','arg1', 'arg2', $named_args, 'arg4', $one_more);
```
output: 

    argv: {
        "0": "arg0",
        "1": "arg1",
        "2": "arg2",
        "4": "arg4"
    }
    vars: {
        "var1": "var1 value",
        "var2": "var2 value",
        "var3": "var3 value"
    }
    $var1 var1 value
    $var2 var2 value
    $var3 var3 value



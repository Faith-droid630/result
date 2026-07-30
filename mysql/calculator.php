<?php
session_start();
if (!isset($_SESSION['display'])) {
    $_SESSION['display'] = '0';
    $_SESSION['stored'] = '';
    $_SESSION['operator'] = '';
}
if (isset($_POST['key'])) {
    $key = $_POST['key'];

    if (is_numeric($key)) {
        // Digit pressed
        if ($_SESSION['display'] == '0') {
            $_SESSION['display'] = $key;
        } else {
            $_SESSION['display'] .= $key;
        }

    } elseif ($key == '.') {
        // Decimal point
        if (strpos($_SESSION['display'], '.') === false) {
            $_SESSION['display'] .= '.';
        }

    } elseif ($key == 'clear') {
        $_SESSION['display'] = '0';
        $_SESSION['stored'] = '';
        $_SESSION['operator'] = '';

    } elseif ($key == 'delete') {
        $_SESSION['display'] = substr($_SESSION['display'], 0, -1);
        if ($_SESSION['display'] == '') {
            $_SESSION['display'] = '0';
        }

    } elseif ($key == '=') {
        if ($_SESSION['operator'] != '') {
            $a = floatval($_SESSION['stored']);
            $b = floatval($_SESSION['display']);
            if ($_SESSION['operator'] == '+') $answer = $a + $b;
            if ($_SESSION['operator'] == '-') $answer = $a - $b;
            if ($_SESSION['operator'] == '*') $answer = $a * $b;
            if ($_SESSION['operator'] == '/') $answer = $b == 0 ? 'Error' : $a / $b;
            $_SESSION['display'] = $answer;
            $_SESSION['stored'] = '';
            $_SESSION['operator'] = '';
        }

    } else {
        $_SESSION['stored'] = $_SESSION['display'];
        $_SESSION['operator'] = $key;
        $_SESSION['display'] = '0';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Calculator</title>
<style>
  body {
    font-family: sans-serif;
    text-align: center;
    margin-top: 40px;
    background-color: #eeeeee;
  }

  .calculator {
    width: 240px;
    margin: auto;
    background-color: #333333;
    padding: 15px;
    border-radius: 10px;
  }

  #display {
    width: 100%;
    height: 45px;
    font-size: 22px;
    text-align: right;
    margin-bottom: 8px;
    box-sizing: border-box;
    background-color: #c9e4ca;
    border: none;
    border-radius: 5px;
    padding: 5px;
  }

  .keys {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
  }

  button {
    height: 45px;
    font-size: 18px;
    background-color: #f5f5f5;
    border: none;
    border-radius: 5px;
  }

  .op {
    background-color: #ffa94d;
    color: white;
  }

  .clear {
    background-color: #ff8080;
    color: white;
  }

  .equals {
    background-color: #63c463;
    color: white;
  }
</style>
</head>
<body>

<div class="calculator">
  <form method="POST">
    <input type="text" id="display" value="<?php echo htmlspecialchars($_SESSION['display']); ?>" readonly>

    <div class="keys">
      <button class="clear" name="key" value="clear">C</button>
      <button class="clear" name="key" value="delete">DEL</button>
      <button class="op" name="key" value="/">/</button>
      <button class="op" name="key" value="*">*</button>

      <button name="key" value="7">7</button>
      <button name="key" value="8">8</button>
      <button name="key" value="9">9</button>
      <button class="op" name="key" value="-">-</button>

      <button name="key" value="4">4</button>
      <button name="key" value="5">5</button>
      <button name="key" value="6">6</button>
      <button class="op" name="key" value="+">+</button>

      <button name="key" value="1">1</button>
      <button name="key" value="2">2</button>
      <button name="key" value="3">3</button>
      <button class="equals" name="key" value="=">=</button>

      <button name="key" value="0">0</button>
      <button name="key" value=".">.</button>
    </div>
  </form>
</div>

</body>
</html>

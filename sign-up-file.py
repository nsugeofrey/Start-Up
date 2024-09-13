#function validates userpassword.
def sign_up():
  #user = input("Enter Username: ")
  pwd = ("password:")
  confirm_pwd = ("password:")
  if confirm_pwd == pwd:
    enc = confirm_pwd.encode()
    hash_ = hashlib.md5(enc).hexdigest()
    
    with open("test.text", "a") as f:
      f.write("user" + "\n")
      f.write(hash_)

    print("You have registered successfully!")

  else:
    print("password doesnot match!")

sing_up()

#code to validate username and password during login
def login():
  print("-- Login --")
  storedUser = input("Enter Username: ")
  sotredPwd = input("Enter password:  ")
  encodePassword = storedPwd.encode()
  hashPassword = hashlib.md5(encodePassword).hexdigest()
  
  with open("data_container.txt", "r") as f:
    if storedUser in f.read():

      if hashPassword in f.read():
          print(f"welcome {storedUser}!")

      else:
        print("Incorrect Password!")
    else:
      print("user doesn't exist!")

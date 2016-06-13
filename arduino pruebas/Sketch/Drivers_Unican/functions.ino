LiquidCrystal lcd(2, 3, 4, 5, 6 , 7);
int values[13]={460,589,20,435,550,745,414,516,685,486,633,395,1};
char chars[13]={'1','2','3','4','5','6','7','8','9','0','F','D','C'};
boolean flags[13];
int noPressed=378;

char pin[4];
int pinCounter=0;
boolean flagPin=false;
boolean clearPinMenssage=false;
boolean flagBadParameter=false;

boolean dots=false;
unsigned long previousMillis = 0;
unsigned long timeSetDate=0; 
unsigned long timeGetPin=0;
int timeToGetPin=10000;
int timeToSetDate=500;       
const long interval = 1000; 

char commands[5];
boolean commandFlags[6]={false,false,false,false,false,false};

//Valores de entrada
char getPin=1;
char setDate=2;
char openDoor=3;

//Valores de salida
int correct=1;
int aLotTime=2;
int missingParameter=3;
int badParameter=4;
int call=5;
int f1=6;
char pushedKey='K';

void printTime(){
  time_t t = now();
  if(hour(t)<10){
   lcd.setCursor(11,0); 
   lcd.write("0");
   lcd.setCursor(12,0); 
   lcd.print(hour(t));
  }
  else{
    lcd.setCursor(11,0); 
   lcd.print(hour(t));
  }

  
  if(minute(t)<10){
   lcd.setCursor(14,0); 
   lcd.write("0");
   lcd.setCursor(15,0); 
   lcd.print(minute(t));
  }
  else{
    lcd.setCursor(14,0); 
   lcd.print(minute(t));
  }

  if(day(t)<10){
   lcd.setCursor(0,0); 
   lcd.write("0");
   lcd.setCursor(1,0); 
   lcd.print(day(t));
  }
  else{
    lcd.setCursor(0,0); 
   lcd.print(day(t));
  }

  if(month(t)<10){
   lcd.setCursor(3,0); 
   lcd.write("0");
   lcd.setCursor(4,0); 
   lcd.print(month(t));
  }
  else{
    lcd.setCursor(3,0); 
   lcd.print(month(t));
  }


   if(year(t)<10){
   lcd.setCursor(6,0); 
   lcd.write("0");
   lcd.setCursor(7,0); 
   lcd.print(year(t));
  }
  else{
    lcd.setCursor(6,0); 
   lcd.print(year(t));
  }
}

void msgPutCard(){
  clearLine();
  lcd.setCursor(0,1);
  lcd.write("Pase su tarjeta");
}

void clearLine(){
  lcd.setCursor(0, 1);
  lcd.write("                ");
}

void clearBuffer(){
  for(int i=0;i<6;i++){
    commandFlags[i]=false;
  }
}

void printChar(char c,int pos){
  lcd.setCursor(pos, 1);
  lcd.write(c);
}

void printString(char s[16]){
  clearLine();
  lcd.setCursor(0, 1);
  lcd.write(s);
}

void endGetPin(){
  pinCounter=0;
  flagPin=false;
  clearPinMenssage=false;
  clearBuffer();
  commands[0]=0;
  msgPutCard();
}

void timeExceded(){
  
  if((millis()-timeSetDate)>=timeToSetDate && commands[0]==setDate){
    clearBuffer();
    commands[0]=0;
    Serial.println(missingParameter);
  }
  if((millis()-timeGetPin)>=timeToGetPin && commands[0]==getPin){
    endGetPin();
    Serial.println(aLotTime);
  }
}

void readBuffer(){

if(Serial.available()){
    char readValue=Serial.read();
    if(!commandFlags[0]){
      if(readValue==getPin){
        printString("Introduce pin");
        commands[0]=readValue;
        commandFlags[0]=true;
        flagPin=true;
        timeGetPin=millis();
      }
      else if(readValue==setDate){
        commands[0]=readValue;
        commandFlags[0]=true;
        timeSetDate=millis();
      }
      else if(readValue==openDoor){
        Serial.println("abrir puerta");
      }
    }
    else if(!commandFlags[1]){
      if(commands[0]==setDate){
        commands[1]=readValue;
        commandFlags[1]=true;
        if(readValue>24){
          flagBadParameter=true;
        }
      }
    }
    else if(!commandFlags[2]){
      if(commands[0]==setDate){
        commands[2]=readValue;
        commandFlags[2]=true;
        if(readValue>60){
          flagBadParameter=true;
        }
      }
    }
    else if(!commandFlags[3]){
      if(commands[0]==setDate){
        commands[3]=readValue;
        commandFlags[3]=true;
        if(readValue>31){
          flagBadParameter=true;
        }
      }
    }
    else if(!commandFlags[4]){
      if(commands[0]==setDate){
        commands[4]=readValue;
        commandFlags[4]=true;
        if(readValue>12){
          flagBadParameter=true;
        }
      }
    }
    else if(!commandFlags[5]){
      if(commands[0]==setDate){
         if(!flagBadParameter && readValue<100){
            setTime(commands[1],commands[2],00,commands[3],commands[4],readValue);
            Serial.println(correct);
            printTime();
         }
         else{
           Serial.println(badParameter);
           flagBadParameter=false;
         }
        clearBuffer();
        timeSetDate=0;
        commands[0]=0;
        
      }
    }
  }
  timeExceded();
}

void scanKeyPad(){
  for(int i=0;i<13;i++){
    int analogValue = analogRead(A0);
    if(analogValue>values[i]-5 && analogValue<values[i]+5 && !flags[i]){
      //Serial.println(chars[i]);
      if(chars[i]=='D'){
            if(pinCounter>0){
              pinCounter--;
              printChar(' ',pinCounter);
            }
          }
          else if(chars[i]=='F'){
            if(!flagPin){
              printString("Funcion 1");
              pinCounter=0;
              Serial.println(f1);
              delay(1000);
              msgPutCard();
            }
          }else if(chars[i]=='C'){
            if(!flagPin){
              printString("Llamar");
              pinCounter=0;
              Serial.println(call);
              delay(1000);
              msgPutCard();
            }
          }else{
            //printChar(chars[j+(i*3)],pinCounter);
            if(flagPin){
              if(!clearPinMenssage){
                clearLine();
                clearPinMenssage=true;
              }
              Serial.println(pushedKey);
              printChar('*',pinCounter);
              pin[pinCounter]=chars[i];
              pinCounter++;
            }
          }
      flags[i]=true;
    }
    else if(analogValue>noPressed-5 && analogValue<noPressed+5 && flags[i]){
      if(pinCounter==4 && flags[i]){
          Serial.print(correct);
          Serial.println(pin);
          endGetPin();
        }
      flags[i]=false;
    }
  }
}


void accesControlInit(int bauds){
  Serial.begin(bauds);
  analogWrite(9,0);
  lcd.begin(16, 2);
  lcd.setCursor(0,0);
  lcd.write("  /  /       :  ");
  setTime(00,00,00,00,00,00);
  msgPutCard();
  printTime();
  for(int i=0;i<12;i++){
    flags[i]=false;
  }
}

void secondBySecond(){
  unsigned long currentMillis = millis();
  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;
    printTime();
    if (dots) {
      lcd.setCursor(13,0);
      lcd.write(":");
      dots=false;
    } else {
      lcd.setCursor(13,0);
      lcd.write(" ");
      dots=true;
    }
  }
}



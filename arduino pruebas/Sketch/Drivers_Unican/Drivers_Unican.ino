#include <LiquidCrystal.h>
#include <Time.h>

void setup() {
  accesControlInit(9600);
}

void loop() {
  secondBySecond();
  readBuffer();
  scanKeyPad();
  delay(50); 
}

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

//Pines
const int PIN_BOMBA    = 16;
const int PIN_VALVULA1 = 4;
const int PIN_VALVULA2 = 5;
const int PIN_SENSOR1  = 6;
const int PIN_SENSOR2  = 7;
const int PIN_SENSOR3  = 15;
const int TRIG_PIN     = 17;
const int ECHO_PIN     = 18;

//Configuración
const char* SSID       = "INFINITUM88B3_plus";
const char* PASSWORD   = "DDgAWZ2G9T";
const char* SERVER_URL = "http://192.168.0.239/Valvulas/procesarEsp.php";

const float ALTURA_TANQUE_CM  = 19.0;
const float FACTOR_CONVERSION = 7.5;
const unsigned long INTERVALO = 2000;

//Variables de flujo
volatile int pulsos1 = 0, pulsos2 = 0, pulsos3 = 0;
portMUX_TYPE mux = portMUX_INITIALIZER_UNLOCKED;
unsigned long ultimoEnvio = 0;

//Interrupciones
void IRAM_ATTR isr1() { portENTER_CRITICAL_ISR(&mux); pulsos1++; portEXIT_CRITICAL_ISR(&mux); }
void IRAM_ATTR isr2() { portENTER_CRITICAL_ISR(&mux); pulsos2++; portEXIT_CRITICAL_ISR(&mux); }
void IRAM_ATTR isr3() { portENTER_CRITICAL_ISR(&mux); pulsos3++; portEXIT_CRITICAL_ISR(&mux); }

//Leer y reiniciar contador
float leerFlujo(volatile int &contador) {
  portENTER_CRITICAL(&mux);
  int p = contador;
  contador = 0;
  portEXIT_CRITICAL(&mux);
  return (p / FACTOR_CONVERSION) / (INTERVALO / 60000.0);
}

//Nivel del tanque (HC-SR04)
float nivelTanque() {
  digitalWrite(TRIG_PIN, LOW);  delayMicroseconds(2);
  digitalWrite(TRIG_PIN, HIGH); delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);

  long dur = pulseIn(ECHO_PIN, HIGH, 30000);
  if (dur == 0) return -1;

  float nivel = ALTURA_TANQUE_CM - (dur * 0.034 / 2.0);
  return constrain((nivel / ALTURA_TANQUE_CM) * 100.0, 0, 100);
}

//Cambiar estado de un relevador 
void setRele(int pin, int estado) {
  int nivel = (estado == 1) ? LOW : HIGH;
  if (digitalRead(pin) != nivel) digitalWrite(pin, nivel);
}

//Conexión
void enviarDatos(float f1, float f2, float f3, float nivel) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("WiFi desconectado, reconectando...");
    WiFi.reconnect();
    return;
  }

  HTTPClient http;
  http.setTimeout(5000);
  http.begin(SERVER_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String body = "sf1=" + String(f1, 2) + "&sf2=" + String(f2, 2)
              + "&sf3=" + String(f3, 2) + "&nivel=" + String(nivel, 1);

  int code = http.POST(body);

  if (code == 200) {
    JsonDocument doc(256);
    if (!deserializeJson(doc, http.getString())) {
      setRele(PIN_BOMBA,    doc["estado1"] | 0);
      setRele(PIN_VALVULA1, doc["estado2"] | 0);
      setRele(PIN_VALVULA2, doc["estado3"] | 0);
    }
  } else {
    Serial.println("Error HTTP: " + String(code));
  }

  http.end();
}

//Setup
void setup() {
  Serial.begin(115200);

  pinMode(PIN_BOMBA,    OUTPUT); digitalWrite(PIN_BOMBA,    HIGH);
  pinMode(PIN_VALVULA1, OUTPUT); digitalWrite(PIN_VALVULA1, HIGH);
  pinMode(PIN_VALVULA2, OUTPUT); digitalWrite(PIN_VALVULA2, HIGH);
  pinMode(TRIG_PIN,     OUTPUT);
  pinMode(ECHO_PIN,     INPUT);
  pinMode(PIN_SENSOR1, INPUT_PULLUP);
  pinMode(PIN_SENSOR2, INPUT_PULLUP);
  pinMode(PIN_SENSOR3, INPUT_PULLUP);

  attachInterrupt(digitalPinToInterrupt(PIN_SENSOR1), isr1, RISING);
  attachInterrupt(digitalPinToInterrupt(PIN_SENSOR2), isr2, RISING);
  attachInterrupt(digitalPinToInterrupt(PIN_SENSOR3), isr3, RISING);

  WiFi.begin(SSID, PASSWORD);
  Serial.print("Conectando");
  while (WiFi.status() != WL_CONNECTED) { delay(500); Serial.print("."); }
  Serial.println("\nListo. IP: " + WiFi.localIP().toString());
}

//Loop
void loop() {
  // Activacion manual por serial solo prueba
  if (Serial.available()) {
    while (Serial.available()) Serial.read();
    Serial.println(">> Bomba manual 5s");
    setRele(PIN_BOMBA, 1);
    delay(5000);
    setRele(PIN_BOMBA, 0);
  }

  if (millis() - ultimoEnvio >= INTERVALO) {
    ultimoEnvio = millis();

    float f1 = leerFlujo(pulsos1);
    float f2 = leerFlujo(pulsos2);
    float f3 = leerFlujo(pulsos3);
    float nv = nivelTanque();

    Serial.printf("F1:%.2f F2:%.2f F3:%.2f Nivel:%.1f%%\n", f1, f2, f3, nv);
    enviarDatos(f1, f2, f3, nv);
  }

  delay(10);
}
# 🎨 Dirección de diseño — Panel Clínica Benites

Este archivo guarda el prompt de dirección de diseño a usar cuando se le pida a Claude
(o a cualquier IA) un ajuste visual del panel Filament. Complementa las instrucciones
puntuales de cada tarea — no las reemplaza.

**Cuándo usarlo**: pegar el bloque de abajo al final del prompt cuando se pida un
**cambio visual grande**. Para ajustes chicos y puntuales (ej. una scrollbar, un
tamaño de fuente puntual) no hace falta — ahí alcanza con decir qué se quiere y Claude
sigue el mismo criterio de siempre ya documentado en `MEMORIA.md`.

```
<direccion_diseno_clinica_benites>
Este es el panel administrativo de una clínica real (Filament 5.7.6 + Tailwind).
Lo usan recepcionistas, médicos y administración durante su jornada de trabajo — no es
una landing page ni un producto de consumo. Priorizá siempre claridad y confianza por
encima de originalidad llamativa. "Distintivo" acá significa "se siente cuidado y
profesional", no "sorprende y divierte".

Evitá el default genérico de IA (fondos blancos planos, azul/púrpura de stock, Inter en
todos lados, tarjetas con sombra idéntica) pero evitá con la misma fuerza el extremo
opuesto: nada de color que compita con datos clínicos/financieros, nada de tipografía
decorativa donde hay números que un médico necesita leer rápido y sin ambigüedad.

Tipografía: la que ya está en uso en el proyecto es la que manda — no introduzcas una
familia nueva sin que se pida explícitamente. Si en algún momento se define tipografía
de marca, priorizá legibilidad de números/tablas por encima de personalidad.

Color: los 2 colores de marca ya definidos son navy #0C447C y verde azulado #0F6E56.
Usalos con la misma lógica que ya se aplicó en el resto del panel (ver MEMORIA.md,
sección de widgets y sección del rediseño del topbar): navy para jerarquía/autoridad
(topbar, elementos primarios), verde azulado para estados positivos/clínicos
(cobrado, confirmado, disponible), gris neutro para lo operativo. No toques la paleta
semántica global de Filament (->colors()) sin pedirlo explícitamente — los cambios de
color van scoped a selectores específicos, para no arriesgar badges/botones ya
confirmados en otras partes del panel.

Movimiento: microinteracciones sutiles está bien (hover, transiciones de 150-200ms),
nunca animación decorativa que distraiga de una pantalla que alguien puede estar
mirando durante una consulta o una llamada con un paciente.

Antes de escribir CSS: seguí el mismo criterio que ya se usa en este proyecto — no
asumas selectores de memoria. Confirmá contra el código fuente real del tag de
Filament que está instalado (ver composer.lock) cuáles son las clases exactas y su
especificidad, antes de proponer overrides. Si hace falta !important, decilo
explícitamente y explicá por qué (mismo patrón ya documentado en MEMORIA.md).

Antes de tocar código en cambios grandes (no un ajuste de una línea): proponé 2-3
direcciones visuales concretas primero, explicá qué prioriza cada una y qué arriesga,
y esperá confirmación antes de implementar — no implementes de una la primera idea.

Nombrá explícitamente qué defaults estás evitando y por qué, así la decisión queda
documentada (mismo estilo ya usado en MEMORIA.md: "se descartó X por Y, se eligió Z").
</direccion_diseno_clinica_benites>
```

## Por qué está armado así

- **No es un prompt de "actuá como diseñador senior"** — ese estilo de rol no es lo que
  mejor funciona con Claude para diseño. Lo que sí funciona, según el propio equipo de
  Anthropic (`anthropics/claude-cookbooks`, notebook
  `coding/prompting_for_frontend_aesthetics.ipynb`), es guiar dimensiones concretas
  (tipografía, color, movimiento) por separado, dar referencias sin ser demasiado
  prescriptivo, y nombrar explícitamente qué defaults evitar — en vez de pedir
  "hazlo bonito" o adoptar una persona.
- Ese notebook está pensado para productos de cara al público (SaaS, landings) donde
  "sorprender y deleitar" es el objetivo. Un panel clínico interno tiene el objetivo
  opuesto en parte: confianza y velocidad de lectura por encima de sorpresa visual —
  por eso el bloque de arriba toma la *técnica* (dimensiones separadas, defaults
  explícitos) pero no el objetivo estético literal del notebook.
- Referencia directa si se quiere profundizar:
  https://github.com/anthropics/claude-cookbooks/blob/main/coding/prompting_for_frontend_aesthetics.ipynb

## Cómo usarlo

Pegarlo tal cual (el bloque entre triple backtick) al final del prompt al pedir un
cambio visual grande. Para ajustes chicos y puntuales no hace falta — ahí ya alcanza
con decir qué se quiere y Claude sigue el mismo criterio de siempre en el proyecto.

---

**Nota**: este archivo se guardó en el repo (26 ago 2026) para que la dirección de
diseño quede versionada junto con el resto del proyecto, en vez de vivir solo como un
prompt pegado a mano cada vez. Ver `MEMORIA.md` (sección 8.1 y afines) para el
historial concreto de decisiones de branding/diseño ya tomadas siguiendo este criterio.

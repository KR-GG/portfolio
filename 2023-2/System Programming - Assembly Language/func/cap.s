.text
cap: .global cap
	ldrb   r1, [r0]
	cmp    r1, #'a'
	rsbges r2, r1, #'z'
	subge  r1, r1, #32
	strgeb r1, [r0]
	bx     lr

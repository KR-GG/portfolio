.text
cap: .global cap
	ldrb   r1, [r0]
	cmp    r1, #0
	bxeq   lr

	cmp    r1, #'a'
	rsbges r2, r1, #'z'
	subge  r1, r1, #32
	strgeb r1, [r0]

	add    r0, r0, #1
    b      cap

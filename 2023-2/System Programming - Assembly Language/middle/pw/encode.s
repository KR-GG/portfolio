.extern encode_chr
print_encode: .global print_encode
	@ print_encode (char* str)
	@               r0
	push
	ldrb	r1, [r0]

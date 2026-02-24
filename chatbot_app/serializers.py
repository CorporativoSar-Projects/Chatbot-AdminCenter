from rest_framework import serializers
from .models import PuestoSAP, InformeSAP


class PuestoSAPSerializer(serializers.ModelSerializer):
    class Meta:
        model = PuestoSAP
        fields = '__all__'


class InformeSAPSerializer(serializers.ModelSerializer):
    class Meta:
        model = InformeSAP
        fields = '__all__'

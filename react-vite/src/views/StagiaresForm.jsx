import React, { useEffect, useState } from "react";
import { useForm, Controller } from "react-hook-form";

import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { DesktopDatePicker, DatePicker, LocalizationProvider } from "@mui/x-date-pickers";
import { 
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Box,
  useMediaQuery,
  Button,
  TextField,
  RadioGroup,
  Radio,
  FormControlLabel,
  FormLabel,
  CircularProgress
} from "@mui/material";

import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from "yup";
import axiosClient from "../axios-client";
import { useNavigate, useParams } from "react-router-dom";
import dayjs from "dayjs";
import { useStateContext } from "../contexts/ContextProvider";
import { useDisplayContext } from "../contexts/DisplayContext";




export default function StagiaresForm() {

  const {id} = useParams()
  const [stagiare, setStagiare] = useState({
    nom: "",
    prenom: "",
    date_naissance: undefined,
    domaine_formation: "",
    diplomes_obtenues: "",
    intitule_formation: "",
    duree_formation: null,
    montant: null,
    lieu_formation: "",
    filiale_id: null,
  })

  const {filiales, getFiliales, setFiliales} = useDisplayContext()
  const {setNotification, filiale, role, user, fetchUser} = useStateContext()

  const useScheme = yup.object({
    nom: yup.string().required("le champ nom est obligatoire"),
    prenom: yup.string().required("le champ prenom est obligatoire"),
    domaine_formation: yup.string().required('ce champ est obligatoire'),
    diplomes_obtenues: yup.string().required("ce champ est obligatoire"),
    date_naissance: yup.date().required("le champs date de naissance est obligatoire"),
    intitule_formation: yup.string().required('ce champ est obligatoire'),
    lieu_formation: yup.string().required('ce champ est obligatoire'),
    duree_formation: yup.number().required('veillez ajouter une valeur a ce champs'),
    montant: yup.number().required('ce champ est obligatoire'),
  })
  const { handleSubmit, control, setError, reset, getValues, setValue, register, formState, formState: { errors } } = useForm({
    mode: "onChange",
    resolver: yupResolver(useScheme)
  });

  const {isSubmitting, isValid, isSubmitted, isSubmitSuccessful} = formState                   
  const isNonMobile = useMediaQuery("(min-width:600px)");
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  

  const onSubmit = (data) => {
    if (stagiare.id) 
    {
      console.log("data : ", data)
      const updateStagiare = {
        nom: data.nom,
        prenom: data.prenom,
        domaine_formation: data.domaine_formation,
        diplomes_obtenues: data.diplomes_obtenues,
        date_naissance: dayjs(data.date_naissance).format("YYYY-MM-DD"),
        intitule_formation: data.intitule_formation,
        lieu_formation: data.lieu_formation,
        duree_formation: data.duree_formation,
        montant: data.montant,
        filiale_id: filiale.id ? filiale.id : data.filiale_id,
      }
      axiosClient.put(`/stagiares/${stagiare.id}`, updateStagiare)
      .then(() => {
        //TODO show notification
        setNotification("stagaire was successfuly Update")
        navigate('/stagiares')
      })
      .catch(err => {
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response?.data.errors
          })        
        }
      })
    } 
    else 
    {
      const Add_stagiare = {
        nom: data.nom,
        prenom: data.prenom,
        domaine_formation: data.domaine_formation,
        diplomes_obtenues: data.diplomes_obtenues,
        date_naissance: dayjs(data.date_naissance).format("YYYY-MM-DD"),
        intitule_formation: data.intitule_formation,
        lieu_formation: data.lieu_formation,
        duree_formation: data.duree_formation,
        montant: data.montant,
        filiale_id: filiale.id ? filiale.id : data.filiale_id,
      }
      console.log(Add_stagiare)
      axiosClient.post('/stagiares', Add_stagiare)
      .then(() => {
        setNotification("le stagiare à bien été saiséer")
        navigate('/stagiares')
      })
      .catch((err) => {
        console.log(err)
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response.response.data.errors
          })       
        }
      })
    }  
  }


  const getStagiare = () => {
    setLoading(true)
    axiosClient.get(`stagiares/${id}`)
    .then(({data}) => {
      setStagiare({
        nom: data.data.nom,
        prenom: data.data.prenom,
        domaine_formation: data.data.domaine_formation,
        diplomes_obtenues: data.data.diplomes_obtenues,
        date_naissance: dayjs(data.data.date_naissance),
        intitule_formation: data.data.intitule_formation,
        lieu_formation: data.data.lieu_formation,
        duree_formation: data.data.duree_formation,
        montant: data.data.montant,
        filiale_id: data.data.filiale_id,
      })
      reset({...data.data})
      setLoading(false)
    })
    .catch((err) => {
      console.log(err)
    })
  }


  if (id) {
    useEffect(() => {
      getStagiare()
      console.log(stagiare)
    }, [])
  }

  useEffect(() => {
    getFiliales()
    fetchUser()
  }, [])

  return (
    <>
      {!loading && <div className="card animated fadeInDown">
        <Box mt="20px">
          <form onSubmit={handleSubmit(onSubmit)}>
            <Box
              display="grid" 
              gap="30px" 
              gridTemplateColumns="repeat(6, minmax(0, 1fr))"
            >
              <TextField
                label="Nom"
                {...register("nom")}
                onChange={ev => setStagiare({...stagiare, nom: ev.target.value})}
                value={stagiare.nom}
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.nom ? true : false}
                helperText={errors.nom && errors.nom?.message}
              />
              <TextField
                label="Prenom"
                {...register("prenom")}
                onChange={ev => setStagiare({...stagiare, prenom: ev.target.value})}
                value={stagiare.prenom}
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.prenom ? true : false}
                helperText={errors.prenom && errors.prenom?.message}
              />
              <TextField 
                label="domaine de formation"
                {...register("domaine_formation")}
                onChange={ev => setStagiare({...stagiare, domaine_formation: ev.target.value})}
                value={stagiare.domaine_formation}
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.domaine_formation ? true : false}
                helperText={errors.domaine_formation && errors.domaine_formation?.message}
              />
              <TextField 
                label="diplomes obtenues"
                {...register("diplomes_obtenues")}
                onChange={ev => setStagiare({...stagiare, diplomes_obtenues: ev.target.value})}
                value={stagiare.diplomes_obtenues}
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.diplomes_obtenues ? true : false}
                helperText={errors.diplomes_obtenues && errors.diplomes_obtenues?.message}
              />

              <Controller
                control={control}
                name="date_naissance"
                render={({ field: { onChange } }) => (
                  <LocalizationProvider dateAdapter={AdapterDayjs}>
                    <DatePicker
                      label={"date de naissance"} 
                      sx={{ gridColumn: "span 2" }}
                      format="DD/MM/YYYY"
                      value={stagiare?.date_naissance}
                      onChange={(event) => {  
                        onChange(event)
                        setStagiare({...stagiare, date_naissance: event})
                      }}
                      slotProps={{
                        textField: {
                          error: errors.date_naissance ? true : false,
                          helperText: errors.date_naissance?.message 
                        }
                      }}
                    />
                  </LocalizationProvider>
                )}
              />
              <TextField
                label="intitule de formation"
                {...register("intitule_formation")}
                onChange={ev => setStagiare({...stagiare, intitule_formation: ev.target.value})}
                value={stagiare.intitule_formation}
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.intitule_formation ? true : false}
                helperText={errors.intitule_formation && errors.intitule_formation?.message}
              />
              <TextField 
                label="lieu de formation"
                {...register("lieu_formation")}
                onChange={ev => setStagiare({...stagiare, lieu_formation: ev.target.value})}
                value={stagiare.lieu_formation}
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.lieu_formation ? true : false}
                helperText={errors.lieu_formation && errors.lieu_formation?.message}
              />
              <TextField
                type="number"               
                label="duree de formation"
                {...register("duree_formation")}
                onChange={ev => setStagiare({...stagiare, duree_formation: ev.target.value})}
                value={stagiare.duree_formation}
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.duree_formation ? true : false}
                helperText={errors.duree_formation && errors.duree_formation?.message}
              />
              <TextField 
                type="number"
                label="montant"
                {...register("montant")}
                onChange={ev => setStagiare({...stagiare, montant: ev.target.value})}
                value={stagiare.montant}
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.montant ? true : false}
                helperText={errors.montant && errors.montant?.message}
              />
              {(role === 'admin') && 
              <FormControl variant="outlined" sx={{ width: "300px" }}>
                { <InputLabel id="demo-simple-select-label"> filiale  </InputLabel>}
                <Select
                  value={stagiare.filiale_id}
                  {...register("filiale_id")}
                  sx={{ gridColumn: "span 2" }}
                  label="filiale"
                  onChange={ev => setStagiare({...stagiare, filiale_id: ev.target?.value})}
                >
                  {(Object.keys(filiales).length !== 0) && filiales?.map(filiale => (
                    <MenuItem value={filiale.id} key={filiale.id}> {filiale.name} </MenuItem>
                  ))}
                </Select>
              </FormControl>
              } 

            </Box>
            <Box display="flex" justifyContent="end" mt="20px">
              <Button  type="submit" color="success" variant="contained">
                  {id ? "Update" : "Create New" }  Stagiare
              </Button>
            </Box>
          </form>
        </Box>
        {errors?.server?.message &&
          <div className="alert">
            {Object.keys(errors?.server?.message).map(key => (
              <p>{errors?.server?.message[key][0]}</p>
            ))}
          </div>}
      </div>}
      {loading && <CircularProgress disableShrink /> }
    </>
  )
}
